<?php

namespace FredBradley\SmugMug\Auth;

use Closure;
use LogicException;
use Psr\Http\Message\RequestInterface;
use Saloon\Contracts\Authenticator;
use Saloon\Http\PendingRequest;

/**
 * Signs requests with OAuth 1.0a and HMAC-SHA1 (RFC 5849), as SmugMug requires for anything beyond public data.
 *
 * The signature covers the method, the URL without its query, every query parameter and, for a form-encoded body,
 * every body parameter. JSON and raw (upload) bodies are not part of it. The connector signs the PSR request once it
 * is final (SmugMugConnector::handlePsrRequest), so the signature covers exactly what is sent; set() only marks the
 * request as one to sign.
 *
 * Without a token it signs the first step of the OAuth flow (getRequestToken, with oauth_callback); with the request
 * token and oauth_verifier it signs the last (getAccessToken).
 */
final class OAuth1Authenticator implements Authenticator
{
    /** @var Closure(): string */
    private Closure $nonce;

    /** @var Closure(): int */
    private Closure $clock;

    /**
     * @param  array<string, string>  $protocolParameters  Further oauth_* parameters for this request, such as
     *                                                     oauth_callback or oauth_verifier.
     * @param  (Closure(): string)|null  $nonce  Makes the nonce; random by default.
     * @param  (Closure(): int)|null  $clock  Gives the timestamp; the current time by default.
     */
    public function __construct(
        #[\SensitiveParameter] private readonly string $consumerKey,
        #[\SensitiveParameter] private readonly string $consumerSecret,
        #[\SensitiveParameter] private readonly ?string $token = null,
        #[\SensitiveParameter] private readonly ?string $tokenSecret = null,
        private readonly array $protocolParameters = [],
        ?Closure $nonce = null,
        ?Closure $clock = null,
    ) {
        foreach (array_keys($protocolParameters) as $name) {
            if (! str_starts_with($name, 'oauth_')) {
                throw new LogicException("\"$name\" is not an OAuth protocol parameter; send it in the query or body instead.");
            }
        }

        $this->nonce = $nonce ?? static fn (): string => bin2hex(random_bytes(16));
        $this->clock = $clock ?? static fn (): int => time();
    }

    /**
     * Nothing to add yet: the signature is made from the final PSR request, in the connector.
     */
    public function set(PendingRequest $pendingRequest): void {}

    /**
     * The request with its Authorization header: OAuth oauth_consumer_key="…", oauth_nonce="…", …,
     * oauth_signature="…".
     */
    public function sign(RequestInterface $request): RequestInterface
    {
        $oauth = $this->oauthParameters();
        $oauth['oauth_signature'] = $this->signature($request->getMethod(), (string) $request->getUri(), [...self::requestParameters($request), ...self::pairs($oauth)]);

        $header = collect($oauth)
            ->map(fn (string $value, string $name) => self::encode($name).'="'.self::encode($value).'"')
            ->implode(', ');

        return $request->withHeader('Authorization', 'OAuth '.$header);
    }

    /**
     * The HMAC-SHA1 signature of a request, base64-encoded.
     *
     * @param  list<array{0: string, 1: string}>  $parameters  Every parameter as [name, value], decoded.
     */
    public function signature(string $method, string $url, array $parameters): string
    {
        $key = self::encode($this->consumerSecret).'&'.self::encode($this->tokenSecret ?? '');

        return base64_encode(hash_hmac('sha1', self::baseString($method, $url, $parameters), $key, true));
    }

    /**
     * The signature base string: METHOD&encoded-base-URL&encoded-normalised-parameters (RFC 5849, 3.4.1).
     *
     * @param  list<array{0: string, 1: string}>  $parameters
     */
    public static function baseString(string $method, string $url, array $parameters): string
    {
        $encoded = array_map(fn (array $pair) => [self::encode($pair[0]), self::encode($pair[1])], $parameters);

        usort($encoded, fn (array $a, array $b) => [$a[0], $a[1]] <=> [$b[0], $b[1]]);

        $normalised = implode('&', array_map(fn (array $pair) => $pair[0].'='.$pair[1], $encoded));

        return strtoupper($method).'&'.self::encode(self::baseUrl($url)).'&'.self::encode($normalised);
    }

    /**
     * The URL as the signature covers it: scheme and host in lower case, the port only when it isn't the scheme's
     * default, the path, and no query or fragment (RFC 5849, 3.4.1.2).
     */
    public static function baseUrl(string $url): string
    {
        $parts = parse_url($url);

        if (! is_array($parts) || ! isset($parts['scheme'], $parts['host'])) {
            throw new LogicException("Cannot sign a request to \"$url\": it is not an absolute URL.");
        }

        $scheme = strtolower($parts['scheme']);
        $port = $parts['port'] ?? null;
        $port = $port !== null && ! ($scheme === 'http' && $port === 80) && ! ($scheme === 'https' && $port === 443) ? ':'.$port : '';

        return $scheme.'://'.strtolower($parts['host']).$port.($parts['path'] ?? '/');
    }

    /**
     * Percent-encoding as OAuth defines it (RFC 3986: everything but letters, digits and -._~).
     */
    public static function encode(string $value): string
    {
        return rawurlencode($value);
    }

    /**
     * Whether the authenticator has a token, so acts for a user rather than only as the application.
     */
    public function hasToken(): bool
    {
        return $this->token !== null && $this->token !== '';
    }

    /**
     * What a dump shows: no secrets.
     *
     * @return array<string, mixed>
     */
    public function __debugInfo(): array
    {
        return ['consumerKey' => '[redacted]', 'consumerSecret' => '[redacted]', 'token' => $this->hasToken() ? '[redacted]' : null, 'tokenSecret' => '[redacted]'];
    }

    /**
     * @return array<string, mixed>
     */
    public function __serialize(): array
    {
        throw new LogicException('An OAuth1Authenticator holds secrets and cannot be serialised.');
    }

    /**
     * @return array<string, string>
     */
    private function oauthParameters(): array
    {
        $parameters = [
            'oauth_consumer_key' => $this->consumerKey,
            'oauth_nonce' => ($this->nonce)(),
            'oauth_signature_method' => 'HMAC-SHA1',
            'oauth_timestamp' => (string) ($this->clock)(),
            'oauth_version' => '1.0',
        ];

        if ($this->hasToken()) {
            $parameters['oauth_token'] = (string) $this->token;
        }

        return [...$parameters, ...$this->protocolParameters];
    }

    /**
     * The query's parameters and, for a form-encoded body, the body's, each as [name, value] (a name may repeat).
     *
     * @return list<array{0: string, 1: string}>
     */
    private static function requestParameters(RequestInterface $request): array
    {
        $parameters = self::parse($request->getUri()->getQuery());

        if (str_starts_with(strtolower($request->getHeaderLine('Content-Type')), 'application/x-www-form-urlencoded')) {
            $body = $request->getBody();
            $parameters = [...$parameters, ...self::parse((string) $body)];

            if ($body->isSeekable()) {
                $body->rewind();
            }
        }

        return $parameters;
    }

    /**
     * @return list<array{0: string, 1: string}>
     */
    private static function parse(string $query): array
    {
        $pairs = [];

        foreach (explode('&', $query) as $pair) {
            if ($pair === '') {
                continue;
            }

            [$name, $value] = array_pad(explode('=', $pair, 2), 2, '');
            $pairs[] = [urldecode($name), urldecode($value)];
        }

        return $pairs;
    }

    /**
     * @param  array<string, string>  $parameters
     * @return list<array{0: string, 1: string}>
     */
    private static function pairs(array $parameters): array
    {
        return array_map(fn (string $name, string $value) => [$name, $value], array_keys($parameters), array_values($parameters));
    }
}
