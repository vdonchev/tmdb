<?php

namespace App\Service;

use App\Dto\TrailerDto;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class KinocheckService
{
    public function __construct(private HttpClientInterface $httpClient)
    {
    }

    /**
     * @param string $imdbId
     * @return array
     * @throws TransportExceptionInterface
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     */
    public function getTrailers(string $imdbId): array
    {
        $result = $this->httpClient->request('GET', 'https://api.kinocheck.com/trailers', [
            'query' => ['tmdb_id' => $imdbId, 'language' => 'en'],
        ]);

        if ($result->getStatusCode() !== 200) {
            return [];
        }

        return $result->toArray();
    }

    /**
     * @throws \DateMalformedStringException
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function getFirstTrailer(string $tmdbId): ?TrailerDto
    {
        if (empty($trailers = $this->getTrailers($tmdbId)) || array_key_exists('error', $trailers)) {
            return null;
        }

        return TrailerDto::fromArray($trailers[0]);
    }
}
