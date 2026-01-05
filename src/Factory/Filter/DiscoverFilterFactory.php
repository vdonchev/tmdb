<?php

namespace App\Factory\Filter;

use App\Enum\ListType;
use App\Enum\MovieSort;
use App\Filter\DiscoverFilter;
use DateMalformedStringException;
use DateTimeImmutable;

class DiscoverFilterFactory
{
    public const array DEFAULT_LANGUAGES = [
        "en",
        "fr",
        "de",
        "es",
        "it",
        "sv",
        "da",
        "no",
        "nb",
        "nn",
        "fi",
        "nl",
        "pl",
        "cs",
        "sk",
        "hu",
        "ro",
        "bg",
        "el",
        "pt",
        "ja",
        "ko"
    ];

    /**
     * @throws DateMalformedStringException
     */
    public function fromList(ListType $list, int $page, int $interval = 30, bool $includeAdult = false): DiscoverFilter
    {
        $fromTo = match ($list) {
            ListType::Upcoming => [
                'gte' => new DateTimeImmutable(),
                'lte' => new DateTimeImmutable('+' . $interval . ' days')
            ],
            ListType::Trending => [
                'gte' => new DateTimeImmutable('-' . $interval . ' days'),
                'lte' => new DateTimeImmutable('-1 day')
            ]
        };

        return new DiscoverFilter(
            page: max(1, $page),
            primaryReleaseDateGte: $fromTo['gte'],
            primaryReleaseDateLte: $fromTo['lte'],
            sortBy: MovieSort::PopularityDesc,
            includeAdult: $includeAdult,
            withOriginalLanguage: self::DEFAULT_LANGUAGES,
            withRuntimeGte: 60
        );
    }

    public function fromArray(array $data): DiscoverFilter
    {
        return new DiscoverFilter(
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
            withRuntimeGte: $data['with_runtime.gte'] ?? null,
            withRuntimeLte: $data['with_runtime.lte'] ?? null,
        );
    }
}
