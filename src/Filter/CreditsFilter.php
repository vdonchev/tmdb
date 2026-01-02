<?php

namespace App\Filter;

final readonly class CreditsFilter implements FilterInterface
{
    public function __construct(
        public string $language,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            language: $data['language'] ?? 'en-US',
        );
    }

    public function toArray(): array
    {
        return [
            'language' => $this->language,
        ];
    }
}
