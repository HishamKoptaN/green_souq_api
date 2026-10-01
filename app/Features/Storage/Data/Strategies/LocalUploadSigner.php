<?php

namespace App\Features\Storage\Data\strategies;

use App\Features\Storage\Data\Contracts\DirectUploadSignerInterface;

class LocalUploadSigner implements DirectUploadSignerInterface
{
    public function generateSignature(string $folder = 'uploads', array $options = []): array
    {
        return [
            'provider' => 'local',
            'file_path' => rtrim(ltrim($folder, '/'), '/').'/'.$options['public_id'].'.'.ltrim(
                $options['extension'],
                '.',
            ),
        ];
    }
}
