<?php

namespace App\Features\Storage\Data\Contracts;

interface DirectUploadSignerInterface
{
    public function generateSignature(string $folder = 'uploads', array $options = []): array;
}
