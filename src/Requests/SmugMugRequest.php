<?php

namespace FredBradley\SmugMug\Requests;

use FredBradley\SmugMug\Data\Data;
use FredBradley\SmugMug\Exceptions\ServerErrorException;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\SmugMugApiException;
use FredBradley\SmugMug\SmugMugConnector;
use FredBradley\SmugMug\Support\Payload;
use Illuminate\Support\Facades\Cache;
use JsonException;
use LogicException;
use Saloon\CachePlugin\Contracts\Cacheable;
use Saloon\CachePlugin\Contracts\Driver;
use Saloon\CachePlugin\Drivers\LaravelCacheDriver;
use Saloon\CachePlugin\Helpers\CacheKeyHelper;
use Saloon\CachePlugin\Traits\HasCaching;
use Saloon\Enums\Method;
use Saloon\Enums\PipeOrder;
use Saloon\Http\PendingRequest;
use Saloon\Http\Request;
use Saloon\Http\Response;

/**
 * What every request to SmugMug shares: its query options (Options), the shape of the response and the model it maps
 * to (for dto()), and optional response caching.
 */
abstract class SmugMugRequest extends Request implements Cacheable
{
    use HasCaching;

    /** One object, as the request's model. */
    public const ONE = 'one';

    /** A page of objects, as a Collection of the request's model. */
    public const LIST = 'list';

    /** The envelope's Response object as an array, for actions that answer with no object of their own. */
    public const RESPONSE = 'response';

    /** The decoded body. */
    public const RAW = 'raw';

    /** Whatever the Locator says: a Collection for Objects or a List, a model for one Object, else the Response. */
    public const AUTO = 'auto';

    /** Nothing: dto() is null once SmugMug said it succeeded. */
    public const NONE = 'none';

    protected string $responseShape = self::NONE;

    /** @var class-string<Data>|null */
    protected ?string $dto = null;

    protected bool $cacheable = false;

    protected Options $options;

    /**
     * @param  Options|array<string, mixed>  $options
     */
    protected function setOptions(Options|array $options): void
    {
        $this->options = Options::of($options);
    }

    /**
     * The query options the request is sent with.
     */
    public function options(): Options
    {
        return $this->options ?? new Options;
    }

    /**
     * @return array<string, mixed>
     */
    protected function defaultQuery(): array
    {
        return $this->options()->toArray();
    }

    public function resolveCacheDriver(): Driver
    {
        return new LaravelCacheDriver(Cache::store(config('smugmug.cache.store')));
    }

    public function cacheExpiryInSeconds(): int
    {
        return (int) config('smugmug.cache.seconds', 3600);
    }

    /**
     * @return array<Method>
     */
    protected function getCacheableMethods(): array
    {
        return $this->cacheable && config('smugmug.cache.enabled') ? [Method::GET] : [];
    }

    /**
     * One entry per application and token, so what one user fetched is never served to another, and per query as
     * it is sent.
     */
    protected function cacheKey(PendingRequest $pendingRequest): string
    {
        $connector = $pendingRequest->getConnector();
        $identity = $connector instanceof SmugMugConnector ? $connector->tokenFingerprint() : $connector::class;
        $query = $pendingRequest->query()->all();
        ksort($query);

        return 'smugmug.response.'.sha1($identity.' '.$pendingRequest->getMethod()->value.' '.$pendingRequest->getUrl().' '.http_build_query($query));
    }

    /**
     * Drops a response the cache plugin stored although SmugMug's envelope says the request failed.
     */
    public function boot(PendingRequest $pendingRequest): void
    {
        if ($this->getCacheableMethods() === []) {
            return;
        }

        $pendingRequest->middleware()->onResponse(function (Response $response) use ($pendingRequest) {
            if ($response->successful() && ! self::envelopeSucceeded($response)) {
                $this->resolveCacheDriver()->delete(CacheKeyHelper::createHashed($pendingRequest, $this->cacheKey($pendingRequest)));
            }
        }, order: PipeOrder::LAST);
    }

    /**
     * Refuse to send a request SmugMug only answers for a signed-in user, when the connector has no access token:
     * without one SmugMug answers 404 Not Found, which says nothing about the real problem.
     */
    protected static function requireToken(PendingRequest $pendingRequest, string $what): void
    {
        $connector = $pendingRequest->getConnector();

        if ($connector instanceof SmugMugConnector && ! $connector->hasToken()) {
            throw new LogicException($what.' needs an access token: it is the user who signed the request, and without a token SmugMug answers 404 Not Found. Set SMUGMUG_ACCESS_TOKEN and SMUGMUG_ACCESS_TOKEN_SECRET (and SMUGMUG_API_SECRET), or use SmugMug::withToken($token, $secret).');
        }
    }

    /**
     * Whether the request can be retried: a GET, which changes nothing.
     */
    public function isSafe(): bool
    {
        return $this->getMethod() === Method::GET;
    }

    /**
     * @return class-string<Data>|null
     */
    public function dtoClass(): ?string
    {
        return $this->dto;
    }

    public function responseShape(): string
    {
        return $this->responseShape;
    }

    /**
     * The response as the request's model (or a Collection of them, or arrays for an object the package has no
     * model for), null for an answer without a body, or a SmugMugApiException for an error status or an envelope
     * whose Code says the request failed.
     */
    public function createDtoFromResponse(Response $response): mixed
    {
        if (! $response->successful()) {
            throw SmugMugApiException::fromResponse($response);
        }

        if (trim($response->body()) === '') {
            return null;
        }

        if (! $this->answersWithJson()) {
            return $this->mapResponse($response);
        }

        try {
            if (! Payload::succeeded($response)) {
                throw SmugMugApiException::fromResponse($response);
            }

            return $this->mapResponse($response);
        } catch (JsonException) {
            // A successful status with something other than JSON, such as a proxy's or maintenance page.
            throw new ServerErrorException($response);
        }
    }

    /**
     * Whether SmugMug answers this request with its JSON envelope (the OAuth token endpoints answer form-encoded).
     */
    protected function answersWithJson(): bool
    {
        return true;
    }

    /**
     * @throws JsonException
     */
    protected function mapResponse(Response $response): mixed
    {
        return match ($this->responseShape) {
            self::ONE => Payload::object($response, $this->dto),
            self::LIST => Payload::items($response, $this->dto),
            self::RESPONSE => Payload::response($response),
            self::RAW => Payload::json($response),
            self::AUTO => match (Payload::locatorType($response)) {
                'Objects', 'List' => Payload::items($response, $this->dto),
                'Object' => Payload::object($response, $this->dto),
                default => Payload::response($response),
            },
            default => null,
        };
    }

    private static function envelopeSucceeded(Response $response): bool
    {
        try {
            return Payload::succeeded($response);
        } catch (JsonException) {
            return false;
        }
    }
}
