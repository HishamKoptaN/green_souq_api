<?php

namespace App\Core\Services\Storage;

use App\Core\Contracts\DirectUploadSignerInterface;

class CloudinaryUploadSigner implements DirectUploadSignerInterface
{
    public function generateSignature(string $folder = 'uploads', array $options = []): array
    {
        $cloudName = config('cloudinary.cloud_name');
        $apiKey = config('cloudinary.api_key');
        $apiSecret = config('cloudinary.api_secret');
        $timestamp = time();

        $cleanFolder = ltrim($folder, '/');

        $paramsToSign = array_merge([
            'folder' => $cleanFolder,
            'timestamp' => $timestamp,
        ], $options);

        // Convert boolean values to string 'true' / 'false' for Cloudinary signature
        foreach ($paramsToSign as $key => $value) {
            if (is_bool($value)) {
                $paramsToSign[$key] = $value ? 'true' : 'false';
            }
        }

        ksort($paramsToSign);

        $stringToSign = collect($paramsToSign)
            ->map(fn($value, $key) => "{$key}={$value}")
            ->implode('&');

        $signature = sha1($stringToSign . $apiSecret);

        return array_merge([
            'provider' => 'cloudinary',
            'upload_url' => "https://api.cloudinary.com/v1_1/{$cloudName}/auto/upload",
            'file_path' => $cleanFolder,
            'api_key' => $apiKey,
            'timestamp' => $timestamp,
            'signature' => $signature,
        ], $paramsToSign);
    }
}
