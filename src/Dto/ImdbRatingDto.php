<?php

declare(strict_types=1);

namespace App\Dto;

final readonly class ImdbRatingDto
{
    public function __construct(
        public string $imdbId,
        public ?float $rating,
        public ?int $ratingCount,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            imdbId: (string)($data['imdb_id'] ?? ''),
            rating: isset($data['rating']) ? (float)$data['rating'] : null,
            ratingCount: isset($data['rating_count']) ? (int)$data['rating_count'] : null,
        );
    }
}
