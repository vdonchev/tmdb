<?php

namespace App\Dto;

use DateTimeImmutable;
use Symfony\Component\Serializer\Attribute\Ignore;
use Symfony\Component\Serializer\Attribute\SerializedName;

final class MovieDto
{
    public function __construct(
        public bool $adult,

        #[SerializedName('backdrop_path')]
        public ?string $backdropPath,
        public int $budget,

        #[SerializedName('genres')]
        public array $genresRaw,
        public string $homepage,
        public int $id,

        #[SerializedName('imdb_id')]
        public ?string $imdbId,

        #[SerializedName('original_language')]
        public string $originalLanguage,

        #[SerializedName('original_title')]
        public string $originalTitle,
        public string $overview,
        public float $popularity,

        #[SerializedName('poster_path')]
        public ?string $posterPath,

        #[SerializedName('release_date')]
        public ?DateTimeImmutable $releaseDate,
        public int $revenue,
        public int $runtime,
        public string $status,
        public string $tagline,
        public string $title,

        #[SerializedName('vote_average')]
        public float $voteAverage,

        #[SerializedName('vote_count')]
        public int $voteCount,

        #[Ignore]
        public array $genres = [],
    ) {
    }
}
