<?php

namespace FredBradley\SmugMug\Support;

use InvalidArgumentException;

/**
 * Writes the parts of a request path safely. A value that goes in the path (a nickname, an album key, a node id) is
 * encoded, and one that would change which endpoint is called is refused: '', '.' and '..', or anything with a '/'
 * or a '!' (SmugMug's separator between an object and its relationships, as in /api/v2/album/{key}!images).
 */
final class Path
{
    public const PREFIX = '/api/v2';

    /**
     * One segment of a path: a key, an id or a nickname. A list gives several, comma-separated, for SmugMug's
     * multi-get (/api/v2/user/cmac,onethumb).
     *
     * @param  string|int|list<string|int>  $value
     */
    public static function segment(string|int|array $value): string
    {
        if (is_array($value)) {
            if ($value === []) {
                throw new InvalidArgumentException('A multi-get needs at least one key.');
            }

            return implode(',', array_map(fn (string|int $key) => self::segment($key), $value));
        }

        $value = (string) $value;

        if (trim($value) === '' || $value === '.' || $value === '..' || strpbrk($value, '/!?#,') !== false) {
            throw new InvalidArgumentException('"'.$value.'" cannot be used in a SmugMug request path.');
        }

        return rawurlencode($value);
    }

    /**
     * A folder's path below a user's root, such as "Travel/2024", each part encoded. An empty path is the root.
     */
    public static function folder(string $path): string
    {
        $path = trim($path, '/');

        if ($path === '') {
            return '';
        }

        return '/'.implode('/', array_map(fn (string $part) => self::segment($part), explode('/', $path)));
    }

    /**
     * A URI as the caller or SmugMug gives one, for the API's host: "/api/v2/album/SJT3DX!images", the same with
     * https://api.smugmug.com in front (as WebUri-style links are), or one relative to the API root
     * ("album/SJT3DX"). A query string is kept for the connector to merge. A URI on any other host is refused, so
     * a signed request (or the API key) never goes anywhere else.
     */
    public static function uri(string $uri): string
    {
        $uri = trim($uri);

        if (preg_match('#^[a-z][a-z0-9+.-]*://#i', $uri)) {
            $host = strtolower((string) parse_url($uri, PHP_URL_HOST));

            if (strtolower((string) parse_url($uri, PHP_URL_SCHEME)) !== 'https' || $host !== 'api.smugmug.com') {
                throw new InvalidArgumentException('"'.$uri.'" is not on https://api.smugmug.com.');
            }

            $uri = (string) preg_replace('#^https://[^/]+#i', '', $uri);
        }

        [$route, $query] = array_pad(explode('?', $uri, 2), 2, null);

        if (! str_starts_with($route, self::PREFIX.'/') && ! str_starts_with($route, self::PREFIX.'!') && $route !== self::PREFIX) {
            $route = self::PREFIX.'/'.ltrim($route, '/');
        }

        foreach (explode('/', $route) as $segment) {
            if ($segment === '.' || $segment === '..') {
                throw new InvalidArgumentException('"'.$uri.'" is not a path SmugMug serves.');
            }
        }

        return $route.($query === null || $query === '' ? '' : '?'.$query);
    }
}
