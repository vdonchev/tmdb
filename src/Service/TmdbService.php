<?php

namespace App\Service;

use App\Dto\CreditsDto;
use App\Dto\MovieDto;
use App\Dto\MovieResultDto;
use App\Dto\ResultDto;
use App\Filter\CreditsFilter;
use App\Filter\DiscoverFilter;
use App\Filter\FilterInterface;
use App\Filter\MovieFilter;
use App\Repository\GenreRepository;
use DateMalformedStringException;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
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
        #[Autowire('%env(TMDB_ACCESS_TOKEN)%')] private readonly string $token,
        #[Autowire('%env(TMDB_API_BASEURL)%')] private readonly string $url,

        #[Autowire('%env(TMDB_API_PATH_DISCOVER)%')] private readonly string $discoverPath,
        #[Autowire('%env(TMDB_API_PATH_MOVIE)%')] private readonly string $moviePath,
        #[Autowire('%env(TMDB_API_PATH_CREDITS)%')] private readonly string $creditsPath,
    ) {
    }

    /**
     * @param string $movieId
     * @param MovieFilter $filter
     * @return MovieDto
     * @throws ClientExceptionInterface
     * @throws DateMalformedStringException
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function getMovieDetails(string $movieId, MovieFilter $filter): MovieDto
    {
        $data = $this->queryApi($filter, $this->moviePath, [$movieId]);

        return MovieDto::fromArray($data);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function getMovieCredits(string $movieId, CreditsFilter $filter): CreditsDto
    {
        $data = $this->queryApi($filter, $this->creditsPath , [$movieId]);

        return CreditsDto::fromArray($data);
    }

    /**
     * @param DiscoverFilter $filter
     * @return ResultDto
     * @throws ClientExceptionInterface
     * @throws DateMalformedStringException
     * @throws DecodingExceptionInterface
     * @throws InvalidArgumentException
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function discoverMovies(DiscoverFilter $filter): ResultDto
    {
        $data = $this->queryApi($filter, $this->discoverPath);

        $genres = $this->genreRepository->getGenres();

        $data['results'] = array_map(
            function (array $movie) use ($genres) {
                $genresData = array_intersect_key($genres, array_flip($movie['genre_ids']));
                return MovieResultDto::fromArray($movie, array_values($genresData));
            },
            $data['results']
        );

        return ResultDto::fromArray($data);
    }

    /**
     * @param FilterInterface $filter
     * @param string $path
     * @param array $pathParams
     * @return array
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    private function queryApi(FilterInterface $filter, string $path, array $pathParams = []): array
    {
        $response = $this->client->request(
            'GET',
            $this->url . sprintf($path, ...$pathParams),
            [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->token,
                    'accept' => 'application/json',
                ],
                'query' => $filter->toArray(),
            ]
        );

        try {
            return $response->toArray();
        } catch (ClientException $e) {
            if ($response->getStatusCode() === 404) {
                throw new NotFoundHttpException('Resource not found on TMDB', $e);
            }

            throw $e;
        }
    }
}
