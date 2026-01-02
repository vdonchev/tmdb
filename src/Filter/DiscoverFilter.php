<?php

namespace App\Filter;

use App\Enum\MovieSort;
use DateTimeImmutable;

final readonly class DiscoverFilter implements FilterInterface
{
    public function __construct(
        public int $page = 1,
        public ?DateTimeImmutable $primaryReleaseDateGte = null,
        public ?DateTimeImmutable $primaryReleaseDateLte = null,
        public MovieSort $sortBy = MovieSort::PopularityDesc,
        public bool $includeAdult = true,
        public bool $includeVideo = false,
        public string $language = 'en-US',
        public array $withOriginalLanguage = ['en', 'bg'],
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            page: (int)($data['page'] ?? 1),
            primaryReleaseDateGte: $data['primary_release_date.gte'] ?? new DateTimeImmutable(),
            primaryReleaseDateLte: $data['primary_release_date.lte'] ?? new DateTimeImmutable(),
            sortBy: isset($data['sort_by'])
                ? (MovieSort::tryFrom($data['sort_by']) ?? MovieSort::PopularityDesc)
                : MovieSort::PopularityDesc,
            includeAdult: filter_var($data['include_adult'] ?? false, FILTER_VALIDATE_BOOL),
            includeVideo: filter_var($data['include_video'] ?? false, FILTER_VALIDATE_BOOL),
            language: (string)($data['language'] ?? 'en-US'),
            withOriginalLanguage: $data['with_original_language'] ?? [],
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'page' => $this->page,
            'primary_release_date.gte' => $this->primaryReleaseDateGte->format('Y-m-d'),
            'primary_release_date.lte' => $this->primaryReleaseDateLte->format('Y-m-d'),
            'sort_by' => $this->sortBy->value,
            'include_adult' => $this->includeAdult ? 'true' : 'false',
            'include_video' => $this->includeVideo ? 'true' : 'false',
            'language' => $this->language,
            'with_original_language' => implode('|', $this->withOriginalLanguage),
        ], fn($value) => $value !== null);
    }
}
