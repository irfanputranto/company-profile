<?php

namespace App\Support;

use Illuminate\Support\Str;

final class MetaDescription
{
    private const CONTENT_LIMIT = 157;

    public static function make(?string ...$candidates): string
    {
        foreach ($candidates as $candidate) {
            $description = self::plainText($candidate);

            if ($description !== '') {
                return Str::limit($description, self::CONTENT_LIMIT, '...', true);
            }
        }

        return '';
    }

    private static function plainText(?string $value): string
    {
        if ($value === null) {
            return '';
        }

        return Str::squish(html_entity_decode(
            strip_tags($value),
            ENT_QUOTES | ENT_HTML5,
            'UTF-8',
        ));
    }
}
