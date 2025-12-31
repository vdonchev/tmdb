<?php

declare(strict_types=1);

namespace App\Controller;

use App\Dto\TmdbFilterDto;
use App\Enum\ListType;
use App\Enum\MovieSort;
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

        $filter = new TmdbFilterDto(max(1, $page), $fromTo['gte'], $fromTo['lte'], MovieSort::PopularityDesc);

        $resultSet = $this->movieRepository->getResults($filter, $list->value);

        return $this->render(
            'home/index.html.twig',
            ['results' => $resultSet, 'page' => $page, 'list' => $list->value]
        );
    }
}
