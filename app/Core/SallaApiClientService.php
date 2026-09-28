<?php

namespace App\Core;

use App\Features\SallaTokenManager\SallaTokenManagerService;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SallaApiClientService
{
    protected $sallaTokenManagerService;

    private string $baseUrl;

    public function __construct(SallaTokenManagerService $sallaTokenManagerService)
    {
        $this->sallaTokenManagerService = $sallaTokenManagerService;
        $env = config('salla.environment', 'production');
        $this->baseUrl = config("salla.base_url.{$env}.api", 'https://api.salla.sa/admin/v2');
    }

    public function get(string $endpoint, array $query = []): array
    {
        return $this->request('GET', $endpoint, $query);
    }

    public function post(string $endpoint, array $data = []): array
    {
        return $this->request('POST', $endpoint, $data);
    }

    public function patch(string $endpoint, array $data = []): array
    {
        return $this->request('PATCH', $endpoint, $data);
    }

    public function put(string $endpoint, array $data = []): array
    {
        return $this->request('PUT', $endpoint, $data);
    }

    public function delete(string $endpoint): array
    {
        return $this->request('DELETE', $endpoint);
    }

    private function request(string $method, string $endpoint, array $data = []): array
    {
        $url = rtrim($this->baseUrl, '/').'/'.ltrim($endpoint, '/');
        try {
            $response = $this->sendHttpRequest($method, $url, $data);
            if (! $response || ! $response->successful()) {
                $status = $response?->status() ?? 'N/A';
                throw new Exception("Salla API unavailable (HTTP {$status})");
            }

            return $response->json();
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    private function sendHttpRequest(string $method, string $url, array $data = [])
    {
        $token = $this->sallaTokenManagerService->getValidToken();
        if (! $token) {
            Log::info('No valid Salla access token, attempting refresh...');
            if ($this->sallaTokenManagerService->refreshToken(true)) {
                Log::info('Token refreshed successfully');
                $token = $this->sallaTokenManagerService->getValidToken();
            }

            if (! $token) {
                throw new Exception('انتهت صلاحية التوكن وفشل في تجديده. أعد التفويض مع سلة.');
            }
        }

        return $this->makeRequestWithRetry($method, $url, $data, $token);
    }

    private function makeRequestWithRetry(string $method, string $url, array $data, string $token)
    {
        $retries = config('salla.retry.attempts', 3);
        $delay = 2;
        for ($i = 1; $i <= $retries; $i++) {
            try {
                $headers = [
                    'Authorization' => "Bearer {$token}",
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Accept-Language' => 'ar',
                ];
                $http = Http::withHeaders($headers)
                    ->timeout(config('salla.timeout', 60))
                    ->connectTimeout(config('salla.connect_timeout', 15));
                $response = match ($method) {
                    'GET' => $http->get($url, $data),
                    'POST' => $http->post($url, $data),
                    'PUT' => $http->put($url, $data),
                    'PATCH' => $http->patch($url, $data),
                    'DELETE' => $http->delete($url),
                };
                if ($response?->successful()) {
                    return $response;
                }
                Log::error('Salla API Error', [
                    'url' => $url,
                    'attempt' => $i,
                    'status' => $response?->status(),
                    'body' => $response?->body(),
                ]);
            } catch (Exception $e) {
                if ($i === $retries) {
                    throw $e;
                }
                sleep($delay * $i);
            }
        }
        throw new Exception('All Salla API retries failed.');
    }
}
