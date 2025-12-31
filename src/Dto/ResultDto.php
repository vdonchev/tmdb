<?php

namespace App\Dto;

class ResultDto
{
    public function __construct(
        public int $page,
        public array $results,
        public int $totalPages,
        public int $totalResults,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            page: (int)($data['page'] ?? 1),
            results: $data['results'] ?? [],
            totalPages: (int)($data['total_pages'] ?? 0),
            totalResults: (int)($data['total_results'] ?? 0),
        );
    }

    public function hasNextPage(): bool
    {
        return $this->page + 1 <= $this->totalPages;
    }

    public function hasPreviousPage(): bool
    {
        return $this->page - 1 > 0;
    }
}
