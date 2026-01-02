<?php

namespace App\Filter;

interface FilterInterface
{
    public static function fromArray(array $data): self;

    public function toArray(): array;
}
