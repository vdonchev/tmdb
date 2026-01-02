<?php

namespace App\Dto;

final readonly class CastDto
{
    public function __construct(
        public bool $adult,
        public int $gender,
        public int $id,
        public string $knownForDepartment,
        public string $name,
        public string $originalName,
        public float $popularity,
        public ?string $profilePath,
        public int $castId,
        public string $character,
        public string $creditId,
        public int $order,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            adult: $data['adult'] ?? true,
            gender: (int)($data['gender'] ?? 0),
            id: (int)($data['id'] ?? 0),
            knownForDepartment: (string)($data['known_for_department'] ?? ''),
            name: (string)($data['name'] ?? ''),
            originalName: (string)($data['original_name'] ?? ''),
            popularity: (float)($data['popularity'] ?? 0.0),
            profilePath: $data['profile_path'] ?? null,
            castId: (int)($data['cast_id'] ?? 0),
            character: (string)($data['character'] ?? ''),
            creditId: (string)($data['credit_id'] ?? ''),
            order: (int)($data['order'] ?? 0),
        );
    }
}
