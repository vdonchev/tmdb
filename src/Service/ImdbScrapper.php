<?php

namespace App\Service;

use App\Dto\ImdbRatingDto;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class ImdbScrapper
{
    public function __construct(
        private HttpClientInterface $client,
        private CacheInterface $cache,
        private DenormalizerInterface $denormalizer
    ) {
    }

    /**
     * @param string $imdbMovieId
     * @return ImdbRatingDto
     * @throws InvalidArgumentException
     */
    public function getRating(string $imdbMovieId): ImdbRatingDto
    {
        $key = 'imdb_rating_' . $imdbMovieId;

        return $this->cache->get($key, function (ItemInterface $item) use ($imdbMovieId) {
            $url = sprintf('https://www.imdb.com/title/%s/', $imdbMovieId);

            $response = $this->client->request('GET', $url, [
                'headers' => [
                    'User-Agent' => 'Googlebot/2.1 (+http://www.google.com/bot.html)',
                    'Referer' => 'https://google.com',
                    'Accept' => 'text/html',
                    'Accept-Language' => 'en-US,en;q=0.9',
                ]
            ]);

            $content = $response->getContent();

            $rating = null;
            $ratingCount = null;
            if (preg_match('/<script type="application\/ld\+json">(.*?)<\/script>/s', $content, $matches)) {
                $data = json_decode($matches[1], true);

                if (isset($data['aggregateRating'])) {
                    $rating = $data['aggregateRating']['ratingValue'] ?? null;
                    $ratingCount = $data['aggregateRating']['ratingCount'] ?? null;
                }
            }

            $item->expiresAfter($rating ? 86400 : 10);

            return $this->denormalizer->denormalize([
                'imdb_id' => $imdbMovieId,
                'rating' => (float)$rating,
                'rating_count' => $ratingCount,
            ], ImdbRatingDto::class);
        });
    }
}
