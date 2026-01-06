<?php

namespace App\Filter;

use App\Enum\MovieSort;
use DateTimeImmutable;
use Symfony\Component\Serializer\Attribute\Context;
use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;

final readonly class DiscoverFilter implements FilterInterface
{
    public function __construct(
        public int $page = 1,

        #[SerializedName('primary_release_date.gte')]
        #[Context([DateTimeNormalizer::FORMAT_KEY => 'Y-m-d'])]
        public ?DateTimeImmutable $primaryReleaseDateGte = null,

        #[SerializedName('primary_release_date.lte')]
        #[Context([DateTimeNormalizer::FORMAT_KEY => 'Y-m-d'])]
        public ?DateTimeImmutable $primaryReleaseDateLte = null,

        #[SerializedName('sort_by')]
        public MovieSort $sortBy = MovieSort::PopularityDesc,

        #[SerializedName('include_adult')]
        public bool $includeAdult = true,

        #[SerializedName('include_video')]
        public bool $includeVideo = false,

        #[SerializedName('language')]
        public string $language = 'en-US',

        public array $withOriginalLanguage = ['en', 'bg'],

        #[SerializedName('with_runtime.gte')]
        public ?int $withRuntimeGte = null,

        #[SerializedName('with_runtime.lte')]
        public ?int $withRuntimeLte = null,
    ) {
    }

    #[SerializedName('with_original_language')]
    public function getWithOriginalLanguage()
    {
        return implode('|', $this->withOriginalLanguage);
    }
}
