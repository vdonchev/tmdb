<?php

declare(strict_types=1);

namespace App\Controller;

use App\Enum\ListType;
use App\Enum\MovieSort;
use App\Filter\CreditsFilter;
use App\Filter\DiscoverFilter;
use App\Filter\MovieFilter;
use App\Repository\TmdbRepository;
use DateTimeImmutable;
use Psr\Cache\InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
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
        $list = ListType::tryFrom($list) ?? ListType::Upcomming;

        $fromTo = match ($list) {
            ListType::Upcomming => [
                'gte' => new DateTimeImmutable(),
                'lte' => new DateTimeImmutable('+14 days')
            ],
            ListType::Trending => [
                'gte' => new DateTimeImmutable('-14 days'),
                'lte' => new DateTimeImmutable('-1 day')
            ]
        };

        $filter = new DiscoverFilter(
            max(1, $page),
            $fromTo['gte'],
            $fromTo['lte'],
            MovieSort::PopularityDesc,
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
            ]
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
    #[Route('/movie/{id}', name: 'app_movie', methods: ['GET'])]
    public function movie(string $id): Response
    {
        $movie = $this->movieRepository->getMovieDetails($id, MovieFilter::fromArray([]));

        return $this->render('home/movie.html.twig', ['movie' => $movie]);
    }

    #[Route('/movie/{id}/creadits', name: 'app_credits', methods: ['GET'])]
    public function credits(string $id): Response
    {
        $credits = $this->movieRepository->getMovieCredits($id, CreditsFilter::fromArray([]));

        return $this->render('home/credits.html.twig', ['credits' => $credits]);
    }
}
