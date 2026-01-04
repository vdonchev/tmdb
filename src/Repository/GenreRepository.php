<?php

namespace App\Repository;

use Psr\Cache\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Attribute\Target;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

final class GenreRepository
{
    private const array GENRES = [
        28 => "genre.action",
        12 => "genre.adventure",
        16 => "genre.animation",
        35 => "genre.comedy",
        80 => "genre.crime",
        99 => "genre.documentary",
        18 => "genre.drama",
        10751 => "genre.family",
        14 => "genre.fantasy",
        36 => "genre.history",
        27 => "genre.horror",
        10402 => "genre.music",
        9648 => "genre.mystery",
        10749 => "genre.romance",
        878 => "genre.science_fiction",
        10770 => "genre.tv_movie",
        53 => "genre.thriller",
        10752 => "genre.war",
        37 => "genre.western",
    ];

    public function __construct(
        #[Target('tv.cache')] private readonly CacheInterface $cache
    ) {
    }

    /**
     * @throws InvalidArgumentException
     */
    public function getGenres(): array
    {
        $key = 'all_genres';

        return $this->cache->get($key, function (ItemInterface $item) use ($key) {
            $item->expiresAfter(86400);
            return self::GENRES;
        });
    }
}
