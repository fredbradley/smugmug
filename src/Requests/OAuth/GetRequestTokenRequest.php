<?php

namespace FredBradley\SmugMug\Requests\OAuth;

use FredBradley\SmugMug\Auth\RequestToken;
use FredBradley\SmugMug\Requests\SmugMugRequest;
use Saloon\Enums\Method;
use Saloon\Http\Response;

/**
 * The first step of OAuth 1.0a: a request token for the user to approve, signed by the application alone with
 * oauth_callback: GET /services/oauth/1.0a/getRequestToken. Send it with SmugMug::oauth()->requestToken().
 */
class GetRequestTokenRequest extends SmugMugRequest
{
    protected Method $method = Method::GET;

    protected string $responseShape = self::RAW;

    /**
     * @param  string  $callback  Where SmugMug sends the user once they approve, or "oob" for a six-digit code.
     */
    public function __construct(public readonly string $callback = 'oob') {}

    public function resolveEndpoint(): string
    {
        return '/services/oauth/1.0a/getRequestToken';
    }

    protected function answersWithJson(): bool
    {
        return false;
    }

    public function isSafe(): bool
    {
        return false;
    }

    protected function mapResponse(Response $response): RequestToken
    {
        return RequestToken::fromBody($response->body());
    }
}
