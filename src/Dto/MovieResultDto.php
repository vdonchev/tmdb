<?php

namespace App\Dto;

use DateMalformedStringException;
use DateTimeImmutable;
use Symfony\Component\Serializer\Attribute\Ignore;
use Symfony\Component\Serializer\Attribute\SerializedName;

final class MovieResultDto
{
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

        #[SerializedName('release_date')]
        public DateTimeImmutable $releaseDate,
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

    /**
     * @throws DateMalformedStringException
     */
    public static function fromArray(array $data, array $genres): self
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
            genres: $data = $genres,
        );
    }
}
