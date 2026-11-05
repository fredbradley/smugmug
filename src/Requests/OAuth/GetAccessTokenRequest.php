<?php

namespace FredBradley\SmugMug\Requests\OAuth;

use FredBradley\SmugMug\Auth\AccessToken;
use FredBradley\SmugMug\Requests\SmugMugRequest;
use Saloon\Enums\Method;
use Saloon\Http\Response;

/**
 * The last step of OAuth 1.0a: the user's access token, for the request token they approved and the verifier
 * SmugMug handed back, signed with the request token: GET /services/oauth/1.0a/getAccessToken. Send it with
 * SmugMug::oauth()->accessToken().
 */
class GetAccessTokenRequest extends SmugMugRequest
{
    protected Method $method = Method::GET;

    protected string $responseShape = self::RAW;

    public function __construct(#[\SensitiveParameter] public readonly string $verifier) {}

    public function resolveEndpoint(): string
    {
        return '/services/oauth/1.0a/getAccessToken';
    }

    protected function answersWithJson(): bool
    {
        return false;
    }

    public function isSafe(): bool
    {
        return false;
    }

    protected function mapResponse(Response $response): AccessToken
    {
        return AccessToken::fromBody($response->body());
    }
}
