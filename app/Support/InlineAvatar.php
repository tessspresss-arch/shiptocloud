<?php

namespace App\Support;

use Illuminate\Support\Str;

class InlineAvatar
{
    public static function dataUri(string $label, string $background, string $foreground, string $fallback = 'NA'): string
    {
        $initials = self::initials($label, $fallback);
        $svg = sprintf(
            '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 128 128" role="img" aria-label="%s"><rect width="128" height="128" rx="32" fill="%s"/><text x="50%%" y="54%%" dominant-baseline="middle" text-anchor="middle" fill="%s" font-family="Arial, sans-serif" font-size="42" font-weight="700">%s</text></svg>',
            e($label !== '' ? $label : $fallback),
            $background,
            $foreground,
            e($initials)
        );

        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }

    private static function initials(string $label, string $fallback): string
    {
        $parts = preg_split('/\s+/', trim($label)) ?: [];
        $initials = collect($parts)
            ->filter()
            ->take(2)
            ->map(fn (string $part) => Str::upper(Str::substr($part, 0, 1)))
            ->implode('');

        return $initials !== '' ? $initials : Str::upper($fallback);
    }
}
