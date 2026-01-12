<?php

namespace App\Dto;

use DateMalformedStringException;
use DateTimeImmutable;
use Symfony\Component\Serializer\Attribute\Ignore;
use Symfony\Component\Serializer\Attribute\SerializedName;

final class MovieResultDto
{
    #[SerializedName('release_date')]
    public DateTimeImmutable $releaseDate;

    public function __construct(
        public bool $adult,

        #[SerializedName('backdrop_path')]
        public ?string $backdropPath,

        #[SerializedName('genre_ids')]
        public array $genreIds,
        public int $id,

        #[SerializedName('original_language')]
        public string $originalLanguage,

        #[SerializedName('original_title')]
        public string $originalTitle,
        public string $overview,
        public float $popularity,

        #[SerializedName('poster_path')]
        public ?string $posterPath,

        public string $title,
        public bool $video,

        #[SerializedName('vote_average')]
        public float $voteAverage,

        #[SerializedName('vote_count')]
        public int $voteCount,

        #[Ignore]
        public array $genres = [],
    ) {
    }

    public function setReleaseDate(string $date): void
    {
        try {
            $this->releaseDate = new DateTimeImmutable($date);
        } catch (DateMalformedStringException) {
            $this->releaseDate = new DateTimeImmutable();
        }
    }

    public function getReleaseDate(): DateTimeImmutable
    {
        return $this->releaseDate;
    }
}
