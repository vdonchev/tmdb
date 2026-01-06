<?php

namespace App\Dto;

final readonly class CreditsDto
{
    /**
     * @param int $id
     * @param CastDto[] $cast
     * @param CrewDto[] $crew
     * @param CrewDto[] $directors
     */
    public function __construct(
        public int $id,
        public array $cast,
        public array $crew,
        public array $directors = []
    ) {
    }
}
