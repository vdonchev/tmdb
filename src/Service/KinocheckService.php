<?php

namespace App\Service;

use App\Dto\TrailerDto;
use App\Exception\KinocheckApiException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Throwable;

final readonly class KinocheckService
{
    public function __construct(
        private HttpClientInterface $httpClient,
        private DenormalizerInterface $denormalizer
    ) {
    }

    /**
     * @throws KinocheckApiException
     * @throws TransportExceptionInterface
     */
    public function getTrailers(string $imdbId): array
    {
        try {
            $result = $this->httpClient->request('GET', 'https://api.kinocheck.com/trailers', [
                'query' => ['tmdb_id' => $imdbId, 'language' => 'en'],
            ]);

            if ($result->getStatusCode() !== 200) {
                return [];
            }

            return $result->toArray();
        } catch (ClientExceptionInterface $e) {
            if ($e->getResponse()->getStatusCode() === 404) {
                throw new NotFoundHttpException('Resource not found on TMDB', $e);
            }
            throw new KinocheckApiException($e->getMessage(), $e->getCode(), $e);
        } catch (Throwable $e) {
            throw new KinocheckApiException($e->getMessage(), (int)$e->getCode(), $e);
        }
    }

    /**
     * @throws ExceptionInterface
     * @throws KinocheckApiException
     * @throws TransportExceptionInterface
     */
    public function getFirstTrailer(string $tmdbId): ?TrailerDto
    {
        if (empty($trailers = $this->getTrailers($tmdbId)) || array_key_exists('error', $trailers)) {
            return null;
        }

        return $this->denormalizer->denormalize($trailers[0], TrailerDto::class);
    }
}
