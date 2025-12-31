<?php

namespace App\Dto;

use DateMalformedStringException;
use DateTimeImmutable;

class MovieDto
{
    public function __construct(
        public bool $adult,
        public ?string $backdropPath,
        public array $genreIds,
        public int $id,
        public string $originalLanguage,
        public string $originalTitle,
        public string $overview,
        public float $popularity,
        public ?string $posterPath,
        public DateTimeImmutable $releaseDate,
        public string $title,
        public bool $video,
        public float $voteAverage,
        public int $voteCount,
        public array $genres = [],
    ) {
    }

    /**
     * @throws DateMalformedStringException
     */
    public static function fromArray(array $data): self
    {
        return new self(
            adult: $data['adult'] ?? false,
            backdropPath: $data['backdrop_path'] ?? null,
            genreIds: $data['genre_ids'] ?? [],
            id: (int)$data['id'],
            originalLanguage: (string)($data['original_language'] ?? ''),
            originalTitle: (string)($data['original_title'] ?? ''),
            overview: (string)($data['overview'] ?? ''),
            popularity: (float)($data['popularity'] ?? 0),
            posterPath: $data['poster_path'] ?? null,
            releaseDate: new DateTimeImmutable($data['release_date'] ?? ''),
            title: (string)($data['title'] ?? ''),
            video: (bool)($data['video'] ?? false),
            voteAverage: (float)($data['vote_average'] ?? 0.0),
            voteCount: (int)($data['vote_count'] ?? 0),
            genres: $data = [],
        );
    }
}
