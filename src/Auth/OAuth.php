<?php

namespace FredBradley\SmugMug\Auth;

use FredBradley\SmugMug\Enums\Access;
use FredBradley\SmugMug\Enums\Permissions;
use FredBradley\SmugMug\Requests\OAuth\GetAccessTokenRequest;
use FredBradley\SmugMug\Requests\OAuth\GetRequestTokenRequest;
use FredBradley\SmugMug\SmugMugApiException;
use FredBradley\SmugMug\SmugMugConnector;

/**
 * Signing a user in with OAuth 1.0a, from SmugMug::oauth():
 *
 *  1. requestToken() gets a temporary token, signed by the application. Keep it in the session.
 *  2. authorizationUrl() is where to send the user: SmugMug asks them to approve the Access and Permissions asked
 *     for, then sends them to the callback with oauth_token and oauth_verifier (or, for "oob", shows a six-digit
 *     code to type in).
 *  3. accessToken() trades the request token and the verifier for the user's access token, which never expires.
 */
final class OAuth
{
    public const AUTHORIZE_URL = 'https://api.smugmug.com/services/oauth/1.0a/authorize';

    public function __construct(private readonly SmugMugConnector $connector) {}

    /**
     * A request token, for the callback given (smugmug.callback_url by default, "oob" when that is unset).
     */
    public function requestToken(?string $callback = null): RequestToken
    {
        $callback ??= (string) (config('smugmug.callback_url') ?: 'oob');
        $request = new GetRequestTokenRequest($callback);
        $request->authenticate($this->connector->consumerAuthenticator(protocolParameters: ['oauth_callback' => $callback]));

        $response = $this->connector->send($request);

        if (! $response->successful()) {
            throw SmugMugApiException::fromResponse($response);
        }

        return $response->dto();
    }

    /**
     * Where to send the user to approve the request token.
     *
     * @param  array<string, string|int|bool>  $options  SmugMug's other authorize options: allowThirdPartyLogin
     *                                                   (0 or 1), showSignUpButton (true or false), username (to
     *                                                   fill in), viewportScale (0 to 1, for a mobile web view).
     */
    public function authorizationUrl(RequestToken|string $requestToken, Access $access = Access::Full, Permissions $permissions = Permissions::Read, array $options = []): string
    {
        $token = $requestToken instanceof RequestToken ? $requestToken->token : $requestToken;

        $query = [
            'oauth_token' => $token,
            'Access' => $access->value,
            'Permissions' => $permissions->value,
            ...array_map(fn (string|int|bool $value) => is_bool($value) ? ($value ? 'true' : 'false') : (string) $value, $options),
        ];

        return self::AUTHORIZE_URL.'?'.http_build_query($query, '', '&', PHP_QUERY_RFC3986);
    }

    /**
     * The user's access token, for the request token they approved and the oauth_verifier SmugMug sent back (or
     * the six-digit code they typed in).
     */
    public function accessToken(RequestToken $requestToken, #[\SensitiveParameter] string $verifier): AccessToken
    {
        $request = new GetAccessTokenRequest($verifier);
        $request->authenticate($this->connector->consumerAuthenticator($requestToken->token, $requestToken->secret, ['oauth_verifier' => $verifier]));

        $response = $this->connector->send($request);

        if (! $response->successful()) {
            throw SmugMugApiException::fromResponse($response);
        }

        return $response->dto();
    }

    /**
     * The connector acting as the user the access token belongs to.
     */
    public function connectorFor(AccessToken $accessToken): SmugMugConnector
    {
        return $this->connector->withToken($accessToken->token, $accessToken->secret);
    }
}
