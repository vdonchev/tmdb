<?php

namespace App\Service;

use App\Dto\MovieResultDto;
use App\Dto\ResultDto;
use App\Dto\TmdbFilterDto;
use App\Repository\GenreRepository;
use DateMalformedStringException;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class TmdbService
{
    public function __construct(
        private readonly HttpClientInterface $client,
        private readonly GenreRepository $genreRepository,
        #[Autowire('%env(TMDB_ACCESS_TOKEN)%')] private readonly string $apiToken,
        #[Autowire('%env(TMDB_API_BASEURL)%')] private readonly string $apiUrl,
        #[Autowire('%env(TMDB_API_ENDPOINT_DISCOVER)%')] private readonly string $searchKey,
    ) {
    }

    /**
     * @param TmdbFilterDto $filter
     * @return ResultDto
     * @throws ClientExceptionInterface
     * @throws DateMalformedStringException
     * @throws DecodingExceptionInterface
     * @throws InvalidArgumentException
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function queryApi(TmdbFilterDto $filter): ResultDto
    {
        $response = $this->client->request(
            'GET',
            $this->apiUrl . $this->searchKey,
            [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiToken,
                    'accept' => 'application/json',
                ],
                'query' => $filter->toArray(),
            ]
        );

        $genres = $this->genreRepository->getGenres();

        $data = $response->toArray() ?? [];
        $data['results'] = array_map(
            function (array $movie) use ($genres) {
                $genresData = array_intersect_key($genres, array_flip($movie['genre_ids']));
                return MovieResultDto::fromArray($movie, array_values($genresData));
            },
            $data['results']
        );

        return ResultDto::fromArray($data);
    }
}
