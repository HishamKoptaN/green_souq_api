<?php

namespace App\Features\Posts\DTOs;

class CreatePostDTO
{
    public function __construct(
        public readonly int $userId,
        public readonly string $type,
        public readonly ?string $content,
        public readonly array $typeData = [],
        public readonly array $mediaFiles = []
    ) {}

    public static function fromRequest(array $validatedData, int $userId): self
    {
        return new self(
            userId: $userId,
            type: $validatedData['type'],
            content: $validatedData['content'] ?? null,
            typeData: $validatedData['type_data'] ?? [],
            mediaFiles: $validatedData['media'] ?? []
        );
    }
}
