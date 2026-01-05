<?php

declare(strict_types=1);

namespace App\Controller;

use App\Enum\ListType;
use App\Factory\Filter\DiscoverFilterFactory;
use App\Filter\CreditsFilter;
use App\Filter\MovieFilter;
use App\Repository\KinocheckRepository;
use App\Repository\TmdbRepository;
use App\Service\ImdbScrapper;
use DateMalformedStringException;
use Psr\Cache\InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: ['bg' => '', 'en' => '/en'])]
class MovieController extends AbstractController
{
    public function __construct(
        private readonly TmdbRepository $movieRepository,
    ) {
    }

    /**
     * @throws DateMalformedStringException
     * @throws InvalidArgumentException
     */
    #[Route('/', name: 'app_home', methods: ['GET'])]
    public function index(
        DiscoverFilterFactory $discoverFilterFactory,
        #[MapQueryParameter] string $list = '',
        #[MapQueryParameter] int $page = 1,
    ): Response {
        $list = ListType::tryFrom($list) ?? ListType::Upcoming;

        $filter = $discoverFilterFactory->fromList($list, $page);

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
     * @throws InvalidArgumentException
     */
    #[Route('/_turbo/movie/{tmdbId}/trailer', name: 'app_trailer', methods: ['GET'])]
    public function trailer(string $tmdbId, Request $request, KinocheckRepository $kinocheckRepository): Response
    {
        $this->isTurboRequest($request);

        $trailer = $kinocheckRepository->getFirstTrailer($tmdbId);

        return $this->render('_turbo/trailer.html.twig', ['trailer' => $trailer]);
    }

    private function isTurboRequest(Request $request): void
    {
        if (!$request->headers->has('Turbo-Frame')) {
            throw new AccessDeniedHttpException('This endpoint can only be accessed via Turbo Frame.');
        }
    }
}
