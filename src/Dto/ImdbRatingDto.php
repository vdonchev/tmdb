<?php

declare(strict_types=1);

namespace App\Dto;

use Symfony\Component\Serializer\Attribute\SerializedName;

final readonly class ImdbRatingDto
{
    public function __construct(
        #[SerializedName('imdb_id')]
        public string $imdbId,
        public ?float $rating,

        #[SerializedName('rating_count')]
        public ?int $ratingCount,
    ) {
    }
}
