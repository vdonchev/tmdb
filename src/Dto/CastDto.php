<?php

namespace App\Dto;

use Symfony\Component\Serializer\Attribute\SerializedName;

final readonly class CastDto
{
    public function __construct(
        public bool $adult,
        public int $gender,
        public int $id,

        #[SerializedName('known_for_department')]
        public string $knownForDepartment,
        public string $name,

        #[SerializedName('original_name')]
        public string $originalName,
        public float $popularity,

        #[SerializedName('profile_path')]
        public ?string $profilePath,

        #[SerializedName('cast_id')]
        public int $castId,
        public string $character,

        #[SerializedName('credit_id')]
        public string $creditId,
        public int $order,
    ) {
    }
}
