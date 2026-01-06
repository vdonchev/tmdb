<?php

namespace App\Factory\Dto;

use App\Dto\MovieDto;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final readonly class MovieDtoFactory
{
    public function __construct(private DenormalizerInterface $denormalizer)
    {
    }

    /**
     * @throws ExceptionInterface
     */
    public function createFromTmdb(array $data): MovieDto
    {
        $movieDto = $this->denormalizer->denormalize($data, MovieDto::class);

        $movieDto->genres = array_map(fn(array $genre) => $genre['name'], $movieDto->genresRaw);

        return $movieDto;
    }
}
