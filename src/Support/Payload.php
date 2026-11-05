<?php

namespace FredBradley\SmugMug\Support;

use FredBradley\SmugMug\Data\Data;
use FredBradley\SmugMug\Data\Models;
use Illuminate\Support\Collection;
use Saloon\Http\Response;

/**
 * Reads SmugMug's response bodies, which wrap everything in one envelope:
 *
 *     {"Response": {"Uri": "…", "Locator": "Album", "LocatorType": "Object", "Album": {…}, "Pages": {…}},
 *      "Expansions": {"/api/v2/…": {…}}, "Code": 200, "Message": "Ok"}
 *
 * The Locator names the key the payload sits under; LocatorType says whether it is one object ("Object"), a page of
 * them ("Objects") or a plain list ("List"). Pages describes paging, and Expansions holds what _expand asked for,
 * keyed by URI.
 */
final class Payload
{
    /**
     * The decoded JSON body, or null when the body is empty. Throws JsonException for anything that isn't JSON.
     */
    public static function json(Response $response): mixed
    {
        $body = $response->body();

        return trim($body) === '' ? null : json_decode($body, true, flags: JSON_THROW_ON_ERROR);
    }

    /**
     * The envelope, when the body is a JSON object.
     *
     * @return array<string, mixed>|null
     */
    public static function envelope(Response $response): ?array
    {
        $json = self::json($response);

        return is_array($json) && ! array_is_list($json) ? $json : null;
    }

    /**
     * The envelope's Response object.
     *
     * @return array<string, mixed>
     */
    public static function response(Response $response): array
    {
        $inner = self::envelope($response)['Response'] ?? null;

        return is_array($inner) ? $inner : [];
    }

    /**
     * The type of what the response carries, such as "Album" or "AlbumImage".
     */
    public static function locator(Response $response): ?string
    {
        $locator = self::response($response)['Locator'] ?? null;

        return is_string($locator) && $locator !== '' ? $locator : null;
    }

    /**
     * "Object", "Objects" or "List".
     */
    public static function locatorType(Response $response): ?string
    {
        $type = self::response($response)['LocatorType'] ?? null;

        return is_string($type) ? $type : null;
    }

    /**
     * What the response carries under its Locator, as decoded JSON: an object, a list of them, or null.
     */
    public static function payload(Response $response): mixed
    {
        $inner = self::response($response);
        $locator = self::locator($response);

        return $locator !== null ? ($inner[$locator] ?? null) : null;
    }

    /**
     * The one object the response carries, as the given model (or the model registered for its Locator, or an
     * array when there is none). Null when the response carries nothing.
     *
     * @param  class-string<Data>|null  $dto
     * @return Data|array<string, mixed>|null
     */
    public static function object(Response $response, ?string $dto = null): Data|array|null
    {
        $payload = self::payload($response);

        if (is_array($payload) && array_is_list($payload)) {
            $payload = $payload[0] ?? null;
        }

        if (! is_array($payload)) {
            return null;
        }

        $dto ??= Models::for(self::locator($response));

        return $dto !== null ? $dto::from($payload) : $payload;
    }

    /**
     * The objects the response carries (one page of them), as the given model, the model registered for the
     * Locator, or arrays.
     *
     * @param  class-string<Data>|null  $dto
     * @return Collection<int, mixed>
     */
    public static function items(Response $response, ?string $dto = null): Collection
    {
        $payload = self::payload($response);

        if ($payload === null || $payload === []) {
            return collect();
        }

        $items = is_array($payload) && array_is_list($payload) ? $payload : [$payload];
        $dto ??= Models::for(self::locator($response));

        return collect($items)->map(fn (mixed $item) => $dto !== null && is_array($item) ? $dto::from($item) : $item)->values();
    }

    /**
     * The raw items, as arrays, for the paginator.
     *
     * @return list<mixed>
     */
    public static function rawItems(Response $response): array
    {
        $payload = self::payload($response);

        if ($payload === null || $payload === []) {
            return [];
        }

        return is_array($payload) && array_is_list($payload) ? $payload : [$payload];
    }

    /**
     * Response.Pages: Total, Start, Count, RequestedCount, and the URIs of the first, previous, next and last pages.
     *
     * @return array<string, mixed>|null
     */
    public static function pages(Response $response): ?array
    {
        $pages = self::response($response)['Pages'] ?? null;

        return is_array($pages) ? $pages : null;
    }

    /**
     * The objects _expand asked for, keyed by their URI.
     *
     * @return array<string, mixed>
     */
    public static function expansions(Response $response): array
    {
        $expansions = self::envelope($response)['Expansions'] ?? null;

        return is_array($expansions) ? $expansions : [];
    }

    /**
     * Whether the envelope's Code (or the uploader's stat) says the request succeeded. An answer without either
     * does not say otherwise.
     */
    public static function succeeded(Response $response): bool
    {
        $envelope = self::envelope($response);

        if ($envelope === null) {
            return true;
        }

        if (isset($envelope['stat']) && is_string($envelope['stat'])) {
            return strtolower($envelope['stat']) === 'ok';
        }

        $code = $envelope['Code'] ?? null;

        return ! is_numeric($code) || ((int) $code >= 200 && (int) $code < 300);
    }
}
