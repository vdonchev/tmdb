<?php

namespace App\Factory\Dto;

use App\Dto\MovieResultDto;
use App\Repository\GenreRepository;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final readonly class MovieResultDtoFactory
{
    public function __construct(
        private GenreRepository $genreRepository,
        private DenormalizerInterface $denormalizer
    ) {
    }

    public function createFromTmdb(array $data): MovieResultDto
    {
        $movieResultDto = $this->denormalizer->denormalize($data, MovieResultDto::class);
        $genres = $this->genreRepository->getGenres();

        $movieResultDto->genres = array_values(array_intersect_key($genres, array_flip($movieResultDto->genreIds)));

        return $movieResultDto;
    }
}
