<?php

namespace App\Core\Contracts;

interface DirectUploadSignerInterface
{
    public function generateSignature(string $folder = 'uploads', array $options = []): array;
}
