<?php

namespace App\Repository;

use App\Dto\CreditsDto;
use App\Dto\MovieDto;
use App\Dto\ResultDto;
use App\Filter\CreditsFilter;
use App\Filter\DiscoverFilter;
use App\Filter\MovieFilter;
use App\Service\TmdbService;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Attribute\Target;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

final class TmdbRepository
{
    public function __construct(
        #[Target('tv.cache')] private readonly CacheInterface $cache,
        private readonly TmdbService $tmdbService
    ) {
    }

    /**
     * @throws InvalidArgumentException
     */
    public function discoverMovies(DiscoverFilter $filter, string $type): ResultDto
    {
        $key = 'movies_l' . $type . '_page' . $filter->page;
        return $this->cache->get($key, function (ItemInterface $item) use ($key, $filter) {
            $item->expiresAfter(3600);

            return $this->tmdbService->discoverMovies($filter);
        });
    }

    /**
     * @throws InvalidArgumentException
     */
    public function getMovieDetails(string $movieId, MovieFilter $filter): MovieDto
    {
        $key = 'movie_id' . $movieId;
        return $this->cache->get($key, function (ItemInterface $item) use ($movieId, $filter) {
            $item->expiresAfter(3600);

            return $this->tmdbService->getMovieDetails($movieId, $filter);
        });
    }

    /**
     * @throws InvalidArgumentException
     */
    public function getMovieCredits(string $movieId, CreditsFilter $filter): CreditsDto
    {
        $key = 'credits_movie_id' . $movieId;
        return $this->cache->get($key, function (ItemInterface $item) use ($movieId, $filter) {
            $item->expiresAfter(null);

            return $this->tmdbService->getMovieCredits($movieId, $filter);
        });
    }
}
