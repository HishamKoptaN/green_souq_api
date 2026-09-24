<?php

namespace App\Features\S3;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Aws\S3\S3Client;

class S3Service
{
    public function signUpload($path, $contentType)
    {
        $disk = Storage::disk('s3');
        $adapter = $disk->getAdapter();
        if (method_exists($adapter, 'getClient')) {
            $client = $adapter->getClient();
        } else {
            $client = new S3Client([
                'version' => 'latest',
                'region'  => config('filesystems.disks.s3.region'),
                'credentials' => [
                    'key'    => config('filesystems.disks.s3.key'),
                    'secret' => config('filesystems.disks.s3.secret'),
                    'ACL'    => 'public-read',
                ],
            ]);
        }
        $command = $client->getCommand('PutObject', [
            'Bucket' => config('filesystems.disks.s3.bucket'),
            'Key'    => $path,
            'ContentType' => $contentType,
        ]);
        return [
            'upload_url' => (string) $client->createPresignedRequest($command, '+10 minutes')->getUri(),
            'file_path'  => $path,
            'content_type' => $contentType
        ];
    }
}