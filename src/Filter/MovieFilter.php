<?php

namespace App\Filter;

final readonly class MovieFilter implements FilterInterface
{
    public function __construct(
        public string $appendToResponse,
        public string $language,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            appendToResponse: $data['append_to_response'] ?? '',
            language: $data['language'] ?? 'en-US',
        );
    }

    public function toArray(): array
    {
        return [
            'append_to_response' => $this->appendToResponse,
            'language' => $this->language,
        ];
    }
}
