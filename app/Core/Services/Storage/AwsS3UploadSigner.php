<?php

namespace App\Core\Services\Storage;

use App\Core\Contracts\DirectUploadSignerInterface;
use Aws\S3\PostObjectV4;
use Illuminate\Support\Facades\Storage;

class AwsS3UploadSigner implements DirectUploadSignerInterface
{
    public function generateSignature(string $folder = 'uploads', array $options = []): array
    {
        $disk = Storage::disk('s3');
        $client = $disk->getClient();
        $bucket = config('filesystems.disks.s3.bucket');
        $key = $folder.'/'.uniqid().'-${filename}';
        $formInputs = ['key' => $key];
        $optionsConditions = [
            ['acl' => 'private'],
            ['bucket' => $bucket],
            ['starts-with', '$key', $folder],
        ];

        $postObject = new PostObjectV4(
            $client,
            $bucket,
            $formInputs,
            $optionsConditions,
            '+30 minutes'
        );

        return [
            'provider' => 's3',
            'upload_url' => $postObject->getFormAttributes()['action'],
            'fields' => $postObject->getFormInputs(),
        ];
    }
}
