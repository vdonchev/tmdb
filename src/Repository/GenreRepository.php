<?php

namespace App\Repository;

use Psr\Cache\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Attribute\Target;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class GenreRepository
{
    private const array GENRES = [
        28 => "Action",
        12 => "Adventure",
        16 => "Animation",
        35 => "Comedy",
        80 => "Crime",
        99 => "Documentary",
        18 => "Drama",
        10751 => "Family",
        14 => "Fantasy",
        36 => "History",
        27 => "Horror",
        10402 => "Music",
        9648 => "Mystery",
        10749 => "Romance",
        878 => "Science Fiction",
        10770 => "TV Movie",
        53 => "Thriller",
        10752 => "War",
        37 => "Western"
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
