<?php

namespace App\Twig;

use Twig\Attribute\AsTwigFilter;

class AppExtension
{
    #[AsTwigFilter('shorten')]
    public function shortenRatingVotesCount(float $number): string
    {
        if ($number < 1000) {
            return $number;
        }

        if ($number < 100000) {
            return round($number / 1000) . 'K';
        }

        return round($number / 100000) . 'M';
    }
}
