<?php

namespace App\Filter;

use Symfony\Component\Serializer\Attribute\SerializedName;

final readonly class SearchFilter implements FilterInterface
{
    public function __construct(
        public string $query = '',
        public bool $includeAdult = false,
        public string $language = 'en-US',

        #[SerializedName('primary_release_year')]
        public string $primaryReleaseYear = '',
        public int $page = 1,
        public string $region = '',
        public string $year = '',
    ) {
    }

    #[SerializedName('include_adult')]
    public function getIncludeAdult(): string
    {
        return $this->includeAdult ? 'true' : 'false';
    }
}
