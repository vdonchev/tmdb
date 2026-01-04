<?php

declare(strict_types=1);

namespace App\Controller;

use App\Enum\ListType;
use App\Enum\MovieSort;
use App\Filter\CreditsFilter;
use App\Filter\DiscoverFilter;
use App\Filter\MovieFilter;
use App\Repository\TmdbRepository;
use App\Service\ImdbScrapper;
use App\Service\KinocheckService;
use DateMalformedStringException;
use DateTimeImmutable;
use Psr\Cache\InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

#[Route(path: ['bg' => '', 'en' => '/en'])]
class MovieController extends AbstractController
{
    public function __construct(
        private readonly TmdbRepository $movieRepository,
    ) {
    }

    /**
     * @param string $list
     * @param int $page
     * @return Response
     * @throws InvalidArgumentException
     */
    #[Route('/', name: 'app_home', methods: ['GET'])]
    public function index(
        #[MapQueryParameter] string $list = '',
        #[MapQueryParameter] int $page = 1,
    ): Response {
        $list = ListType::tryFrom($list) ?? ListType::Upcoming;

        $fromTo = match ($list) {
            ListType::Upcoming => [
                'gte' => new DateTimeImmutable(),
                'lte' => new DateTimeImmutable('+30 days')
            ],
            ListType::Trending => [
                'gte' => new DateTimeImmutable('-30 days'),
                'lte' => new DateTimeImmutable('-1 day')
            ]
        };

        $filter = new DiscoverFilter(
            page: max(1, $page),
            primaryReleaseDateGte: $fromTo['gte'],
            primaryReleaseDateLte: $fromTo['lte'],
            sortBy: MovieSort::PopularityDesc,
            includeAdult: false,
            withOriginalLanguage: [
                "en",
                "fr",
                "de",
                "es",
                "it",
                "sv",
                "da",
                "no",
                "nb",
                "nn",
                "fi",
                "nl",
                "pl",
                "cs",
                "sk",
                "hu",
                "ro",
                "bg",
                "el",
                "pt",
                "ja",
                "ko"
            ],
            withRuntimeGte: 60
        );

        $results = $this->movieRepository->discoverMovies($filter, $list->value);

        return $this->render(
            'home/index.html.twig',
            ['results' => $results, 'page' => $page, 'list' => $list->value]
        );
    }

    /**
     * @throws InvalidArgumentException
     */
    #[Route('/movie/{id}/{slug}', name: 'app_movie', methods: ['GET'])]
    public function movie(string $id): Response
    {
        $movie = $this->movieRepository->getMovieDetails($id, MovieFilter::fromArray([]));

        return $this->render('home/movie.html.twig', ['movie' => $movie]);
    }

    /**
     * @throws InvalidArgumentException
     */
    #[Route('/_turbo/imdb/{id}', name: 'app_imdb', methods: ['GET'])]
    public function imdb(string $id, ImdbScrapper $scrapper, Request $request): Response
    {
        $this->isTurboRequest($request);

        $rating = $scrapper->getRating($id);

        return $this->render('_turbo/imdb_rating.html.twig', ['rating' => $rating]);
    }

    /**
     * @throws InvalidArgumentException
     */
    #[Route('/_turbo/movie/{id}/credits', name: 'app_credits', methods: ['GET'])]
    public function credits(string $id, Request $request): Response
    {
        $this->isTurboRequest($request);

        $credits = $this->movieRepository->getMovieCredits($id, CreditsFilter::fromArray([]));

        return $this->render('_turbo/credits.html.twig', ['credits' => $credits]);
    }

    /**
     * @throws DateMalformedStringException
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    #[Route('/_turbo/movie/{tmdbId}/trailer', name: 'app_trailer', methods: ['GET'])]
    public function trailer(string $tmdbId, Request $request, KinocheckService $kinocheckService): Response
    {
        $this->isTurboRequest($request);

        $trailer = $kinocheckService->getFirstTrailer($tmdbId);

        return $this->render('_turbo/trailer.html.twig', ['trailer' => $trailer]);
    }

    private function isTurboRequest(Request $request): void
    {
        if (!$request->headers->has('Turbo-Frame')) {
            throw new AccessDeniedHttpException('This endpoint can only be accessed via Turbo Frame.');
        }
    }
}
