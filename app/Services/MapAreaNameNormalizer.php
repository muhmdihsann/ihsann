<?php

namespace App\Services;

use Illuminate\Support\Str;

class MapAreaNameNormalizer
{
    public function provinceKey(string $name): string
    {
        $normalized = Str::upper(trim($name));
        $normalized = preg_replace('/^PROVINSI\s+/u', '', $normalized) ?? '';
        $normalized = preg_replace('/[^[:alnum:]]+/u', ' ', $normalized) ?? '';
        $normalized = trim(preg_replace('/\s+/u', ' ', $normalized) ?? '');

        return match ($normalized) {
            // These two forms are present in the project's Province data and GeoJSON.
            'DI YOGYAKARTA', 'DAERAH ISTIMEWA YOGYAKARTA' => 'YOGYAKARTA',
            'DKI JAKARTA', 'JAKARTA' => 'JAKARTA',
            default => str_replace(' ', '', $normalized),
        };
    }

    public function regencyKey(string $name): string
    {
        $normalized = Str::upper(trim($name));
        $normalized = preg_replace('/^(KAB(?:UPATEN)?|KOTA)\.?\s+/u', '', $normalized) ?? '';
        $normalized = preg_replace('/[^[:alnum:]]+/u', ' ', $normalized) ?? '';

        return str_replace(' ', '', trim(preg_replace('/\s+/u', ' ', $normalized) ?? ''));
    }
}
