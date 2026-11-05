<?php

namespace FredBradley\SmugMug;

use FredBradley\SmugMug\Exceptions\ConflictException;
use FredBradley\SmugMug\Exceptions\FailedException;
use FredBradley\SmugMug\Exceptions\ForbiddenException;
use FredBradley\SmugMug\Exceptions\InvalidRequestException;
use FredBradley\SmugMug\Exceptions\NotFoundException;
use FredBradley\SmugMug\Exceptions\PaymentRequiredException;
use FredBradley\SmugMug\Exceptions\RateLimitedException;
use FredBradley\SmugMug\Exceptions\ServerErrorException;
use FredBradley\SmugMug\Exceptions\UnauthorizedException;
use FredBradley\SmugMug\Support\RateLimit;
use Saloon\Exceptions\Request\RequestException;
use Saloon\Http\Response;

/**
 * An error SmugMug answered with. The API wraps every answer in {"Code", "Message", "Response"}; the uploader in
 * {"stat", "code", "message"}. Either way the message says why the request failed.
 */
class SmugMugApiException extends RequestException
{
    public function __construct(Response $response)
    {
        parent::__construct($response, $this->describe($response));
    }

    /**
     * The exception for SmugMug's answer: a subclass for each kind of error a caller may handle differently.
     */
    public static function fromResponse(Response $response): self
    {
        $status = $response->status();

        return match (true) {
            $response->successful() => new FailedException($response),
            $status === 401 => new UnauthorizedException($response),
            $status === 402 => new PaymentRequiredException($response),
            $status === 403 => new ForbiddenException($response),
            $status === 404 => new NotFoundException($response),
            $status === 409 => new ConflictException($response),
            $status === 429 => new RateLimitedException($response),
            in_array($status, [400, 405, 406, 411, 413, 415, 422], true) => new InvalidRequestException($response),
            $status === 408 || $response->serverError() => new ServerErrorException($response),
            default => new self($response),
        };
    }

    /**
     * SmugMug's explanation of the failure, when it gave one.
     */
    public function errorMessage(): ?string
    {
        return self::messageIn($this->getResponse());
    }

    /**
     * The rate-limit window as the failed response described it.
     */
    public function rateLimit(): RateLimit
    {
        return RateLimit::fromResponse($this->getResponse());
    }

    protected function describe(Response $response): string
    {
        $text = 'SmugMug responded '.$response->status().' to '.self::requestLine($response);
        $page = self::htmlTitle($response);

        if ($page !== null) {
            return $text.' with an HTML '.($response->successful() ? 'page instead of JSON' : 'error page').($page !== '' ? " ('$page')" : '');
        }

        if ($response->body() !== '' && ! json_validate($response->body())) {
            return $response->successful() ? $text.' with a body that is not JSON' : $text.': '.self::clean($response->body());
        }

        $message = self::messageIn($response);

        return $text.($message !== null ? ": $message" : '');
    }

    /**
     * The failed request's method and path, without its query string (which may carry the API key).
     */
    protected static function requestLine(Response $response): string
    {
        $pendingRequest = $response->getPendingRequest();
        $url = $pendingRequest->getUrl();

        return $pendingRequest->getMethod()->value.' '.(parse_url($url, PHP_URL_HOST) === 'upload.smugmug.com' ? 'https://upload.smugmug.com/' : (parse_url($url, PHP_URL_PATH) ?: '/'));
    }

    /**
     * The title of an HTML error page ('' when it has none), or null when SmugMug didn't send HTML.
     */
    private static function htmlTitle(Response $response): ?string
    {
        if (! str_contains(strtolower((string) $response->header('Content-Type')), 'html') && ! str_starts_with(ltrim($response->body()), '<')) {
            return null;
        }

        return preg_match('#<title>(.*?)</title>#is', $response->body(), $matches) ? self::clean(html_entity_decode($matches[1])) : '';
    }

    /**
     * What SmugMug said: the envelope's Message (unless it only restates the status, as "Not Found" does), or the
     * uploader's message. Null when it said nothing the package can read.
     */
    private static function messageIn(Response $response): ?string
    {
        $body = json_decode($response->body(), true);

        if (! is_array($body) || array_is_list($body)) {
            return null;
        }

        foreach (['Message', 'message', 'error', 'error_description'] as $key) {
            $value = self::text($body[$key] ?? null);

            if ($value !== null) {
                return rtrim($value, '.');
            }
        }

        return null;
    }

    private static function text(mixed $value): ?string
    {
        return is_scalar($value) && trim((string) $value) !== '' ? self::clean((string) $value) : null;
    }

    /**
     * Server text fit for a message or a log line: tags and control characters out, one line, at most 200
     * characters.
     */
    private static function clean(string $value): string
    {
        $value = trim((string) preg_replace('/[\x00-\x1F\x7F]+/u', ' ', strip_tags($value)));

        return mb_strlen($value) > 200 ? mb_substr($value, 0, 197).'...' : $value;
    }
}
