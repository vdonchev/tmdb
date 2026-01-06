<?php

namespace App\Repository;

use App\Dto\TrailerDto;
use App\Service\KinocheckService;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

final readonly class KinocheckRepository
{
    public function __construct(
        #[Autowire(service: 'tv.cache')] private CacheInterface $cache,
        private KinocheckService $kinocheckService
    ) {
    }

    /**
     * @throws InvalidArgumentException
     */
    public function getFirstTrailer(string $tmdbId): ?TrailerDto
    {
        $key = 'trailer_' . $tmdbId;

        return $this->cache->get($key, function (ItemInterface $item) use ($tmdbId) {
            $item->expiresAfter(3600);

            return $this->kinocheckService->getFirstTrailer($tmdbId);
        });
    }
}
