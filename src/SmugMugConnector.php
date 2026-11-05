<?php

namespace FredBradley\SmugMug;

use FredBradley\SmugMug\Auth\OAuth;
use FredBradley\SmugMug\Auth\OAuth1Authenticator;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\AdhocBodyRequest;
use FredBradley\SmugMug\Requests\AdhocRequest;
use FredBradley\SmugMug\Requests\SmugMugRequest;
use FredBradley\SmugMug\Resources\HasResources;
use FredBradley\SmugMug\Support\RateLimit;
use InvalidArgumentException;
use JsonSerializable;
use LogicException;
use Psr\Http\Message\RequestInterface;
use Saloon\Config;
use Saloon\Contracts\Authenticator;
use Saloon\Enums\Method;
use Saloon\Exceptions\Request\FatalRequestException;
use Saloon\Exceptions\Request\RequestException;
use Saloon\Http\Auth\QueryAuthenticator;
use Saloon\Http\Connector;
use Saloon\Http\PendingRequest;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\PaginationPlugin\Contracts\HasPagination;
use Saloon\Traits\Plugins\HasTimeout;
use Throwable;

/**
 * The connection to SmugMug for one application (its API key and secret) and, optionally, one user (their OAuth
 * access token and secret), with a resource for every part of the API and ad-hoc calls for any URI.
 *
 * With a token every request is signed with OAuth 1.0a and sees what that user may see; without one, requests carry
 * the API key alone and see public data only.
 */
class SmugMugConnector extends Connector implements HasPagination
{
    use HasResources;
    use HasTimeout;

    public const API_HOST = 'api.smugmug.com';

    public const UPLOAD_HOST = 'upload.smugmug.com';

    protected float $connectTimeout;

    protected float $requestTimeout;

    public function __construct(
        #[\SensitiveParameter] protected string $apiKey,
        #[\SensitiveParameter] protected ?string $apiSecret = null,
        #[\SensitiveParameter] protected ?string $accessToken = null,
        #[\SensitiveParameter] protected ?string $accessTokenSecret = null,
    ) {
        if (trim($apiKey) === '') {
            throw new InvalidArgumentException('A SmugMug connector needs an API key.');
        }

        if ($this->hasToken() && ($apiSecret === null || $apiSecret === '' || $accessTokenSecret === null || $accessTokenSecret === '')) {
            throw new InvalidArgumentException('Signing requests with an access token needs the API secret and the access token secret too.');
        }

        $this->connectTimeout = self::timeout(config('smugmug.timeouts.connect'), Config::$defaultConnectionTimeout);
        $this->requestTimeout = self::timeout(config('smugmug.timeouts.request'), Config::$defaultRequestTimeout);

        if (config('smugmug.retries.times')) {
            $this->tries = (int) config('smugmug.retries.times');
            $this->retryInterval = (int) config('smugmug.retries.interval_ms', 500);
            $this->useExponentialBackoff = true;
            $this->throwOnMaxTries = false;
        }
    }

    /**
     * SmugMug's API host. Every endpoint path starts with /api/v2, as the URIs SmugMug itself returns do.
     */
    public function resolveBaseUrl(): string
    {
        return 'https://'.self::API_HOST;
    }

    /**
     * The same application acting as another user: the access token and secret they gave the application when
     * they signed in (SmugMug::oauth()->accessToken()).
     */
    public function withToken(#[\SensitiveParameter] string $accessToken, #[\SensitiveParameter] string $accessTokenSecret): self
    {
        return new self($this->apiKey, $this->apiSecret, $accessToken, $accessTokenSecret);
    }

    /**
     * The same application with no user: public data only, by API key.
     */
    public function withoutToken(): self
    {
        return new self($this->apiKey, $this->apiSecret);
    }

    /**
     * Whether requests are signed for a user, rather than sent with the API key alone.
     */
    public function hasToken(): bool
    {
        return $this->accessToken !== null && $this->accessToken !== '';
    }

    /**
     * The application's API key (the OAuth consumer key), which is not secret.
     */
    public function apiKey(): string
    {
        return $this->apiKey;
    }

    /**
     * OAuth 1.0a sign-in: get a request token, send the user to SmugMug to approve it, and trade it for their
     * access token.
     */
    public function oauth(): OAuth
    {
        return new OAuth($this);
    }

    /**
     * The application's own signer, for the steps of the OAuth flow (a request token, then the access token).
     *
     * @param  array<string, string>  $protocolParameters
     */
    public function consumerAuthenticator(#[\SensitiveParameter] ?string $token = null, #[\SensitiveParameter] ?string $tokenSecret = null, array $protocolParameters = []): OAuth1Authenticator
    {
        if ($this->apiSecret === null || $this->apiSecret === '') {
            throw new LogicException('Signing in with OAuth needs the API secret (smugmug.api_secret).');
        }

        return new OAuth1Authenticator($this->apiKey, $this->apiSecret, $token, $tokenSecret, $protocolParameters);
    }

    /**
     * A fingerprint of the application and token, so that what one user fetched is never served to another (the
     * response cache keys on it) without the secrets themselves being written anywhere.
     */
    public function tokenFingerprint(): string
    {
        return hash('sha256', $this->apiKey.'|'.($this->accessToken ?? '').'|'.($this->accessTokenSecret ?? ''));
    }

    /**
     * The rate-limit window as a response describes it.
     */
    public function rateLimit(Response $response): RateLimit
    {
        return RateLimit::fromResponse($response);
    }

    /**
     * What a dump shows: no secrets.
     *
     * @return array<string, mixed>
     */
    public function __debugInfo(): array
    {
        return [
            'apiKey' => $this->apiKey,
            'apiSecret' => $this->apiSecret === null ? null : '[redacted]',
            'accessToken' => $this->hasToken() ? '[redacted]' : null,
            'accessTokenSecret' => $this->accessTokenSecret === null ? null : '[redacted]',
        ];
    }

    /**
     * A connector holds secrets, so it is never written out with a queued job or a session; build one from config
     * (or the user's stored token) where it is needed.
     *
     * @return array<string, mixed>
     */
    public function __serialize(): array
    {
        throw new LogicException('A SmugMugConnector cannot be serialised; resolve it from the SmugMug facade or config where it is used.');
    }

    protected function defaultAuth(): Authenticator
    {
        if ($this->hasToken()) {
            return new OAuth1Authenticator($this->apiKey, (string) $this->apiSecret, $this->accessToken, $this->accessTokenSecret);
        }

        return new QueryAuthenticator('APIKey', $this->apiKey);
    }

    /**
     * @return array<string, string>
     */
    protected function defaultHeaders(): array
    {
        return [
            'Accept' => 'application/json',
        ];
    }

    /**
     * The last step before a request is sent: the query string encoded as RFC 3986 (spaces as %20, as OAuth signs
     * them), a refusal of any host but SmugMug's, and the OAuth signature over the request exactly as it goes out.
     */
    public function handlePsrRequest(RequestInterface $request, PendingRequest $pendingRequest): RequestInterface
    {
        $uri = $request->getUri();
        $host = strtolower($uri->getHost());

        if ($uri->getScheme() !== 'https' || ! in_array($host, [self::API_HOST, self::UPLOAD_HOST], true)) {
            throw new LogicException('The SmugMug connector only sends requests to https://'.self::API_HOST.' and https://'.self::UPLOAD_HOST.'.');
        }

        $request = $request->withUri($uri->withQuery(self::rfc3986($uri->getQuery())));

        $authenticator = $pendingRequest->getAuthenticator();

        return $authenticator instanceof OAuth1Authenticator ? $authenticator->sign($request) : $request;
    }

    /**
     * The exception for a failed response, wherever Saloon throws one ($response->throw(), the paginator, retries):
     * SmugMug's own, with its message.
     */
    public function getRequestException(Response $response, ?Throwable $senderException): ?Throwable
    {
        return SmugMugApiException::fromResponse($response);
    }

    /**
     * Retry only a request that changes nothing, and only for a fault on SmugMug's side, the way there, or its rate
     * limit.
     */
    public function handleRetry(FatalRequestException|RequestException $exception, Request $request): bool
    {
        $isSafe = $request instanceof SmugMugRequest ? $request->isSafe() : $request->getMethod() === Method::GET;

        if (! $isSafe) {
            return false;
        }

        return $exception instanceof FatalRequestException
            || in_array($exception->getResponse()->status(), [408, 429], true)
            || $exception->getResponse()->serverError();
    }

    /**
     * Page through a list request, $perPage objects at a time (smugmug.pagination.per_page by default).
     */
    public function paginate(Request $request, ?int $perPage = null): SmugMugPaginator
    {
        return new SmugMugPaginator($this, $request, $perPage ?? (int) config('smugmug.pagination.per_page', 100));
    }

    /**
     * Fetch any URI SmugMug gave you, such as one from an object's Uris ($album->uri('AlbumImages')) or a page's
     * NextPage: a model, a Collection of them, or arrays for what the package has no model for.
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function follow(string $uri, Options|array $options = []): mixed
    {
        $response = $this->send(new AdhocRequest(Method::GET, $uri, $options));

        if (! $response->successful()) {
            throw SmugMugApiException::fromResponse($response);
        }

        return $response->dto();
    }

    /**
     * @param  Options|array<string, mixed>  $options
     */
    public function get(string $uri, Options|array $options = []): Response
    {
        return $this->send(new AdhocRequest(Method::GET, $uri, $options));
    }

    /**
     * @param  array<mixed>|JsonSerializable  $body
     * @param  Options|array<string, mixed>  $options
     */
    public function post(string $uri, array|JsonSerializable $body = [], Options|array $options = []): Response
    {
        return $this->send(new AdhocBodyRequest(Method::POST, $uri, $body, $options));
    }

    /**
     * @param  array<mixed>|JsonSerializable  $body
     * @param  Options|array<string, mixed>  $options
     */
    public function patch(string $uri, array|JsonSerializable $body = [], Options|array $options = []): Response
    {
        return $this->send(new AdhocBodyRequest(Method::PATCH, $uri, $body, $options));
    }

    /**
     * @param  array<mixed>|JsonSerializable  $body
     * @param  Options|array<string, mixed>  $options
     */
    public function put(string $uri, array|JsonSerializable $body = [], Options|array $options = []): Response
    {
        return $this->send(new AdhocBodyRequest(Method::PUT, $uri, $body, $options));
    }

    /**
     * @param  Options|array<string, mixed>  $options
     */
    public function delete(string $uri, Options|array $options = []): Response
    {
        return $this->send(new AdhocRequest(Method::DELETE, $uri, $options));
    }

    /**
     * A query string re-encoded as RFC 3986, each pair kept in order (a name may repeat).
     */
    private static function rfc3986(string $query): string
    {
        $pairs = [];

        foreach (explode('&', $query) as $pair) {
            if ($pair === '') {
                continue;
            }

            [$name, $value] = array_pad(explode('=', $pair, 2), 2, null);
            $pairs[] = rawurlencode(urldecode($name)).($value === null ? '' : '='.rawurlencode(urldecode($value)));
        }

        return implode('&', $pairs);
    }

    /**
     * A timeout from config: seconds, or the default when the value is empty or not positive (a zero would mean
     * waiting forever).
     */
    private static function timeout(mixed $value, float $default): float
    {
        return is_numeric($value) && (float) $value > 0 ? (float) $value : $default;
    }
}
