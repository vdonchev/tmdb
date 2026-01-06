<?php

namespace App\Factory\Dto;

use App\Dto\CastDto;
use App\Dto\CreditsDto;
use App\Dto\CrewDto;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final readonly class CreditsDtoFactory
{
    public function __construct(private DenormalizerInterface $denormalizer)
    {
    }

    /**
     * @throws ExceptionInterface
     */
    public function createFromTmdb(array $data): CreditsDto
    {
        $cast = array_map(
            fn(array $item) => $this->denormalizer->denormalize($item, CastDto::class),
            $data['cast'] ?? []
        );

        $crew = array_map(
            fn(array $item) => $this->denormalizer->denormalize($item, CrewDto::class),
            $data['crew'] ?? []
        );

        $directors = array_values(
            array_filter(
                $crew,
                fn(CrewDto $crew) => $crew->job === 'Director'
            )
        );

        return new CreditsDto(
            id: (int)($data['id'] ?? 0),
            cast: $cast,
            crew: $crew,
            directors: $directors
        );
    }
}
