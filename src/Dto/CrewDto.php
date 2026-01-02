<?php

namespace App\Dto;

final readonly class CrewDto
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
        public string $creditId,
        public string $department,
        public string $job,
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
            creditId: (string)($data['credit_id'] ?? ''),
            department: (string)($data['department'] ?? ''),
            job: (string)($data['job'] ?? ''),
        );
    }
}
