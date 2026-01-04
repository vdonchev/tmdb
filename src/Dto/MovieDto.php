<?php

namespace App\Dto;

use DateMalformedStringException;
use DateTimeImmutable;

final readonly class MovieDto
{
    public function __construct(
        public bool $adult,
        public ?string $backdropPath,
        public int $budget,
        public array $genres,
        public string $homepage,
        public int $id,
        public ?string $imdbId,
        public string $originalLanguage,
        public string $originalTitle,
        public string $overview,
        public float $popularity,
        public ?string $posterPath,
        public ?DateTimeImmutable $releaseDate,
        public int $revenue,
        public int $runtime,
        public string $status,
        public string $tagline,
        public string $title,
        public float $voteAverage,
        public int $voteCount,
    ) {
    }

    /**
     * @throws DateMalformedStringException
     */
    public static function fromArray(array $data): self
    {
        $releaseDate = !empty($data['release_date'])
            ? new DateTimeImmutable($data['release_date'])
            : null;

        $genresData = $data['genres'] ?? [];
        $genres = array_map(fn(array $genre) => $genre['name'], $genresData);

        return new self(
            adult: $data['adult'] ?? false,
            backdropPath: $data['backdrop_path'] ?? null,
            budget: (int)($data['budget'] ?? 0),
            genres: $genres,
            homepage: (string)($data['homepage'] ?? ''),
            id: (int)$data['id'],
            imdbId: $data['imdb_id'] ?? null,
            originalLanguage: (string)($data['original_language'] ?? ''),
            originalTitle: (string)($data['original_title'] ?? ''),
            overview: (string)($data['overview'] ?? ''),
            popularity: (float)($data['popularity'] ?? 0.0),
            posterPath: $data['poster_path'] ?? null,
            releaseDate: $releaseDate,
            revenue: (int)($data['revenue'] ?? 0),
            runtime: (int)($data['runtime'] ?? 0),
            status: (string)($data['status'] ?? ''),
            tagline: (string)($data['tagline'] ?? ''),
            title: (string)($data['title'] ?? ''),
            voteAverage: (float)($data['vote_average'] ?? 0.0),
            voteCount: (int)($data['vote_count'] ?? 0),
        );
    }

    public function getSlug(): string
    {
        $slug = str_replace(' ', '-', $this->title);
        $slug = preg_replace('/[^a-zA-Z0-9\-_]/', '', $slug);

        return strtolower($slug);
    }
}
