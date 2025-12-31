<?php

namespace App\Repository;

use App\Dto\ResultDto;
use App\Dto\TmdbFilterDto;
use App\Service\TmdbService;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Attribute\Target;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

readonly class TmdbRepository
{
    public function __construct(
        #[Target('tv.cache')] private CacheInterface $cache,
        private TmdbService $tmdbService
    ) {
    }

    /**
     * @throws InvalidArgumentException
     */
    public function getResults(TmdbFilterDto $filter, string $type): ResultDto
    {
        $key = 'movies_l' . $type . '_page' . $filter->page;
        return $this->cache->get($key, function (ItemInterface $item) use ($key, $filter) {
            $item->expiresAfter(3600);

            return $this->tmdbService->queryApi($filter);
        });
    }
}
