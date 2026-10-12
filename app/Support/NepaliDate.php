<?php

namespace App\Support;

use DateTimeInterface;
use Nutandc\NepaliDateConverter\DateConverter;

/**
 * Formats a Gregorian (AD) date as a Bikram Sambat (BS) date using Devanagari
 * digits and the Nepali month name, e.g. २०८३ असोज २३.
 */
class NepaliDate
{
    public static function format(?DateTimeInterface $date): string
    {
        if ($date === null) {
            return '';
        }

        try {
            $bs = app(DateConverter::class)->toNepaliFromDateTime($date);
            $parts = $bs->toFormattedArray();

            return trim("{$parts['year']} {$parts['month']} {$parts['day']}");
        } catch (\Throwable) {
            // The converter only covers 1944-2033 AD. Rather than break a PDF
            // for an out-of-range date, fall back to the Gregorian value.
            return $date->format('Y-m-d');
        }
    }
}
