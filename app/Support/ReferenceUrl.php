<?php

namespace App\Support;

use App\Models\ReferenceToken;

class ReferenceUrl
{
    public const PARAMETER = 'rt';

    public const SESSION_KEY = 'reference_token';

    public static function currentToken(): ?string
    {
        $queryToken = request()->query(self::PARAMETER);

        if (is_string($queryToken) && trim($queryToken) !== '') {
            return trim($queryToken);
        }

        $sessionToken = session(self::SESSION_KEY.'.token');

        return is_string($sessionToken) && trim($sessionToken) !== ''
            ? trim($sessionToken)
            : null;
    }

    public static function currentReferenceToken(): ?ReferenceToken
    {
        $token = self::currentToken();

        if (! $token) {
            return null;
        }

        return ReferenceToken::query()
            ->active()
            ->where('token', self::normalizeToken($token))
            ->first();
    }

    public static function route(string $name, mixed $parameters = [], bool $absolute = true): string
    {
        return self::appendToken(route($name, $parameters, $absolute));
    }

    public static function appendToken(string $url, ?string $token = null): string
    {
        $token ??= self::currentToken();

        if (! $token) {
            return $url;
        }

        return self::withQuery($url, [
            self::PARAMETER => self::normalizeToken($token),
        ]);
    }

    public static function withQuery(string $url, array $parameters): string
    {
        [$withoutFragment, $fragment] = array_pad(explode('#', $url, 2), 2, null);
        [$path, $query] = array_pad(explode('?', $withoutFragment, 2), 2, null);

        parse_str($query ?? '', $queryParameters);

        foreach ($parameters as $key => $value) {
            if ($value === null || $value === '') {
                unset($queryParameters[$key]);

                continue;
            }

            $queryParameters[$key] = $value;
        }

        $queryString = http_build_query($queryParameters);
        $result = $path.($queryString ? '?'.$queryString : '');

        return $fragment !== null ? $result.'#'.$fragment : $result;
    }

    public static function displayHost(?string $url): string
    {
        $url = trim((string) $url);

        if ($url === '') {
            return '';
        }

        $parseableUrl = preg_match('/^[a-z][a-z0-9+.-]*:\/\//i', $url)
            ? $url
            : 'https://'.$url;

        $host = parse_url($parseableUrl, PHP_URL_HOST);

        if (is_string($host) && $host !== '') {
            $port = parse_url($parseableUrl, PHP_URL_PORT);

            return $host.($port ? ':'.$port : '');
        }

        [$withoutFragment] = explode('#', $url, 2);
        [$withoutQuery] = explode('?', $withoutFragment, 2);
        [$fallbackHost] = explode('/', $withoutQuery, 2);

        return $fallbackHost;
    }

    public static function normalizeToken(string $token): string
    {
        return str($token)
            ->trim()
            ->upper()
            ->replaceMatches('/[^A-Z0-9]/', '')
            ->limit(32, '')
            ->value();
    }
}
