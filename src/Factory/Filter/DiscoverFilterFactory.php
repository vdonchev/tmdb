<?php

namespace App\Factory\Filter;

use App\Enum\ListType;
use App\Enum\MovieSort;
use App\Filter\DiscoverFilter;
use DateMalformedStringException;
use DateTimeImmutable;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

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

    public function __construct(
        #[Autowire(env: 'TMDB_DISCOVER_ADULT')] private bool $includeAdult,
        #[Autowire(env: 'TMDB_DISCOVER_INTERVAL')] private int $interval,
    ) {
    }

    /**
     * @throws DateMalformedStringException
     */
    public function fromList(ListType $list, int $page): DiscoverFilter
    {
        $fromTo = match ($list) {
            ListType::Upcoming => [
                'gte' => new DateTimeImmutable(),
                'lte' => new DateTimeImmutable('+' . $this->interval . ' days')
            ],
            ListType::Trending => [
                'gte' => new DateTimeImmutable('-' . $this->interval . ' days'),
                'lte' => new DateTimeImmutable('-1 day')
            ]
        };

        return new DiscoverFilter(
            page: max(1, $page),
            primaryReleaseDateGte: $fromTo['gte'],
            primaryReleaseDateLte: $fromTo['lte'],
            sortBy: MovieSort::PopularityDesc,
            includeAdult: $this->includeAdult,
            withOriginalLanguage: self::DEFAULT_LANGUAGES,
            withRuntimeGte: 60
        );
    }
}
