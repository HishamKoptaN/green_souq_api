<?php

namespace App\Features\Storage\Data\Strategies;

use App\Features\Storage\Data\Contracts\DirectUploadSignerInterface;

class CloudinaryUploadSigner implements DirectUploadSignerInterface
{
    public function generateSignature(string $folder = 'uploads', array $options = []): array
    {
        $cloudName = config('cloudinary.cloud_name');
        $apiKey = config('cloudinary.api_key');
        $apiSecret = config('cloudinary.api_secret');
        $timestamp = time();
        $cleanFolder = ltrim($folder, '/');
        if (app()->environment('dev') || app()->environment() === 'dev') {
            $cleanFolder = 'dev' . ($cleanFolder !== '' ? '/' . $cleanFolder : '');
        }
        $extension = $options['extension'] ?? null;
        unset($options['extension']);
        $basePublicId = $options['public_id'] ?? uniqid();
        if ($extension) {
            $ext = ltrim($extension, '.');
            $publicId = $basePublicId . '.' . $ext;
            $folderToSign = $cleanFolder . '/' . $publicId;
            $publicIdToSign = $basePublicId;
        } else {
            $publicId = $basePublicId;
            if (!isset($options['public_id']) && !str_contains($publicId, '${filename}')) {
                $publicId = $basePublicId . '-' . '${filename}';
            }
            $folderToSign = $cleanFolder;
            $publicIdToSign = $publicId;
        }
        $paramsToSign = array_merge([
            'folder' => $folderToSign,
            'public_id' => $publicIdToSign,
            'timestamp' => $timestamp,
        ], $options);
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
            'disk' => 'cloudinary',
            'upload_url' => "https://api.cloudinary.com/v1_1/{$cloudName}/auto/upload",
            'file_path' => $cleanFolder . '/' . $publicId,
            'api_key' => $apiKey,
            'timestamp' => $timestamp,
            'signature' => $signature,
        ], $paramsToSign);
    }
}
