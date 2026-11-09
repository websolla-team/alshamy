<?php

namespace App\Http\Controllers\Front;

final class ContactLinks
{
    public static function telephone(?string $value): ?string
    {
        $value = trim($value ?? '');
        if (! preg_match('/^\+?[0-9][0-9\s().-]*$/D', $value)) {
            return null;
        }

        $digits = preg_replace('/\D/', '', $value);
        if (strlen($digits) < 8 || strlen($digits) > 15) {
            return null;
        }

        return 'tel:'.(str_starts_with($value, '+') ? '+' : '').$digits;
    }

    public static function whatsapp(?string $value): ?string
    {
        $value = trim($value ?? '');
        if ($value === '') {
            return null;
        }

        if (str_starts_with(strtolower($value), 'https://')) {
            $parts = parse_url($value);
            if (! $parts || strtolower($parts['scheme'] ?? '') !== 'https') {
                return null;
            }

            $host = strtolower($parts['host'] ?? '');
            if ($host === 'wa.me') {
                $value = ltrim($parts['path'] ?? '', '/');
            } elseif ($host === 'api.whatsapp.com' && ($parts['path'] ?? '') === '/send') {
                parse_str($parts['query'] ?? '', $query);
                $value = is_string($query['phone'] ?? null) ? $query['phone'] : '';
            } else {
                return null;
            }
        }

        if (! preg_match('/^\+?[0-9][0-9\s().-]*$/D', $value)) {
            return null;
        }

        $digits = preg_replace('/\D/', '', $value);
        if (str_starts_with($digits, '00')) {
            $digits = substr($digits, 2);
        }
        if (preg_match('/^01[0125]\d{8}$/', $digits)) {
            $digits = '20'.substr($digits, 1);
        }
        if (strlen($digits) < 10 || strlen($digits) > 15) {
            return null;
        }

        return 'https://wa.me/'.$digits;
    }
}
