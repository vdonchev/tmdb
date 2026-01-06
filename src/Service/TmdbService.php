<?php

namespace App\Service;

use App\Dto\CreditsDto;
use App\Dto\MovieDto;
use App\Dto\ResultDto;
use App\Exception\TmdbApiException;
use App\Factory\Dto\CreditsDtoFactory;
use App\Factory\Dto\MovieDtoFactory;
use App\Factory\Dto\MovieResultDtoFactory;
use App\Filter\CreditsFilter;
use App\Filter\DiscoverFilter;
use App\Filter\FilterInterface;
use App\Filter\MovieFilter;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Throwable;

final readonly class TmdbService
{
    public function __construct(
        private HttpClientInterface $client,
        private SerializerInterface $serializer,
        private CreditsDtoFactory $creditsDtoFactory,
        private MovieDtoFactory $movieDtoFactory,
        private MovieResultDtoFactory $movieResultDtoFactory,

        #[Autowire('%env(TMDB_ACCESS_TOKEN)%')] private string $token,
        #[Autowire('%env(TMDB_API_BASEURL)%')] private string $url,

        #[Autowire('%env(TMDB_API_PATH_DISCOVER)%')] private string $discoverPath,
        #[Autowire('%env(TMDB_API_PATH_MOVIE)%')] private string $moviePath,
        #[Autowire('%env(TMDB_API_PATH_CREDITS)%')] private string $creditsPath,
    ) {
    }

    /**
     * @throws ExceptionInterface
     * @throws TmdbApiException
     */
    public function getMovieDetails(string $movieId, MovieFilter $filter): MovieDto
    {
        $data = $this->queryApi($filter, $this->moviePath, [$movieId]);

        return $this->movieDtoFactory->createFromTmdb($data);
    }

    /**
     * @throws ExceptionInterface
     * @throws TmdbApiException
     */
    public function getMovieCredits(string $movieId, CreditsFilter $filter): CreditsDto
    {
        $data = $this->queryApi($filter, $this->creditsPath, [$movieId]);

        return $this->creditsDtoFactory->createFromTmdb($data);
    }

    /**
     * @throws TmdbApiException
     */
    public function discoverMovies(DiscoverFilter $filter): ResultDto
    {
        $data = $this->queryApi($filter, $this->discoverPath);

        $data['results'] = array_map(
            fn(array $movie) => $this->movieResultDtoFactory->createFromTmdb($movie),
            $data['results'] ?? []
        );

        return $this->serializer->denormalize($data, ResultDto::class);
    }

    /**
     * @throws TmdbApiException
     */
    private function queryApi(FilterInterface $filter, string $path, array $pathParams = []): array
    {
        try {
            $queryParams = $this->serializer->normalize($filter, null, ['skip_null_values' => true]);

            $response = $this->client->request(
                'GET',
                $this->url . sprintf($path, ...$pathParams),
                [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $this->token,
                        'accept' => 'application/json',
                    ],
                    'query' => $queryParams,
                ]
            );

            return $response->toArray();
        } catch (ClientExceptionInterface $e) {
            if ($e->getResponse()->getStatusCode() === 404) {
                throw new NotFoundHttpException('Resource not found on TMDB', $e);
            }
            throw new TmdbApiException($e->getMessage(), $e->getCode(), $e);
        } catch (Throwable $e) {
            throw new TmdbApiException($e->getMessage(), (int)$e->getCode(), $e);
        }
    }
}
