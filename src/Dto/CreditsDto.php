<?php

namespace App\Dto;

final readonly class CreditsDto
{
    /**
     * @param int $id
     * @param CastDto[] $cast
     * @param CrewDto[] $crew
     */
    public function __construct(
        public int $id,
        public array $cast,
        public array $crew,
    ) {
    }

    public static function fromArray(array $data): self
    {
        $cast = array_map(
            fn(array $item) => CastDto::fromArray($item),
            $data['cast'] ?? []
        );

        $crew = array_map(
            fn(array $item) => CrewDto::fromArray($item),
            $data['crew'] ?? []
        );

        return new self(
            id: (int)($data['id'] ?? 0),
            cast: $cast,
            crew: $crew,
        );
    }

    /**
     * @return array<int, CrewDto>
     */
    public function getDirectors(): array
    {
        return array_filter($this->crew, fn(CrewDto $crew) => $crew->job === 'Director');
    }
}
