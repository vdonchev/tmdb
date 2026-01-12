<?php

declare(strict_types=1);

namespace App\Controller;

use App\Filter\SearchFilter;
use App\Repository\TmdbRepository;
use Psr\Cache\InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: ['bg' => '', 'en' => '/en'])]
class SearchController extends AbstractController
{
    /**
     * @throws InvalidArgumentException
     */
    #[Route('/search', name: 'app_search_index')]
    public function index(
        TmdbRepository $tmdbRepository,
        #[MapQueryParameter] int $page = 1,
        #[MapQueryParameter] string $query = ''
    ): Response
    {
        $searchFilter = new SearchFilter(query: $query, page: $page);

        $results = $tmdbRepository->searchMovies($query, $searchFilter);

        return $this->render('search/index.html.twig', ['results' => $results, 'page' => $page, 'query' => $query ?? '']);
    }
}
