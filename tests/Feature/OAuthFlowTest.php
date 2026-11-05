<?php

use FredBradley\SmugMug\Auth\AccessToken;
use FredBradley\SmugMug\Auth\OAuth;
use FredBradley\SmugMug\Auth\RequestToken;
use FredBradley\SmugMug\Enums\Access;
use FredBradley\SmugMug\Enums\Permissions;
use FredBradley\SmugMug\Exceptions\UnauthorizedException;
use FredBradley\SmugMug\Requests\OAuth\GetAccessTokenRequest;
use FredBradley\SmugMug\Requests\OAuth\GetRequestTokenRequest;
use FredBradley\SmugMug\Tests\Helpers\FakeSmugMug;
use Saloon\Http\Faking\MockResponse;

it('gets a request token, signed by the application with its callback', function () {
    $mockClient = FakeSmugMug::respondWith([GetRequestTokenRequest::class => MockResponse::make('oauth_token=req-token&oauth_token_secret=req-secret&oauth_callback_confirmed=true', 200, ['Content-Type' => 'application/x-www-form-urlencoded'])]);

    $token = FakeSmugMug::connector()->oauth()->requestToken('https://app.test/smugmug/callback');
    $sent = FakeSmugMug::sent($mockClient, GetRequestTokenRequest::class)[0];
    $oauth = FakeSmugMug::oauthParameters($sent);

    expect($token)->toBeInstanceOf(RequestToken::class)
        ->and($token->token)->toBe('req-token')
        ->and($token->secret)->toBe('req-secret')
        ->and($token->callbackConfirmed)->toBeTrue()
        ->and((string) $sent->getUri())->toBe('https://api.smugmug.com/services/oauth/1.0a/getRequestToken')
        ->and($oauth['oauth_callback'])->toBe('https://app.test/smugmug/callback')
        ->and($oauth)->not->toHaveKey('oauth_token')
        ->and(FakeSmugMug::signatureIsValid($sent, tokenSecret: null))->toBeTrue();
});

it('asks for the callback in config, or oob', function () {
    $mockClient = FakeSmugMug::respondWith([GetRequestTokenRequest::class => MockResponse::make('oauth_token=t&oauth_token_secret=s')]);

    FakeSmugMug::connector()->oauth()->requestToken();
    config()->set('smugmug.callback_url', 'https://app.test/configured');
    FakeSmugMug::connector()->oauth()->requestToken();

    $callbacks = array_map(fn ($request) => FakeSmugMug::oauthParameters($request)['oauth_callback'], FakeSmugMug::sent($mockClient, GetRequestTokenRequest::class));

    expect($callbacks)->toBe(['oob', 'https://app.test/configured']);
});

it('builds the authorize URL with the access and permissions asked for', function () {
    $oauth = FakeSmugMug::connector()->oauth();

    expect($oauth->authorizationUrl(new RequestToken('req-token', 'req-secret')))
        ->toBe('https://api.smugmug.com/services/oauth/1.0a/authorize?oauth_token=req-token&Access=Full&Permissions=Read')
        ->and($oauth->authorizationUrl('req-token', Access::Public, Permissions::Modify, ['allowThirdPartyLogin' => 1, 'showSignUpButton' => false, 'username' => 'sam jones']))
        ->toBe('https://api.smugmug.com/services/oauth/1.0a/authorize?oauth_token=req-token&Access=Public&Permissions=Modify&allowThirdPartyLogin=1&showSignUpButton=false&username=sam%20jones');
});

it('trades the request token and verifier for the access token', function () {
    $mockClient = FakeSmugMug::respondWith([GetAccessTokenRequest::class => MockResponse::make('oauth_token=access-token&oauth_token_secret=access-secret')]);

    $token = FakeSmugMug::connector()->oauth()->accessToken(new RequestToken('req-token', 'req-secret'), '123456');
    $sent = FakeSmugMug::sent($mockClient, GetAccessTokenRequest::class)[0];
    $oauth = FakeSmugMug::oauthParameters($sent);

    expect($token)->toBeInstanceOf(AccessToken::class)
        ->and($token->toArray())->toBe(['token' => 'access-token', 'secret' => 'access-secret'])
        ->and($oauth['oauth_token'])->toBe('req-token')
        ->and($oauth['oauth_verifier'])->toBe('123456')
        ->and(FakeSmugMug::signatureIsValid($sent, tokenSecret: 'req-secret'))->toBeTrue()
        ->and(FakeSmugMug::connector()->oauth()->connectorFor($token)->hasToken())->toBeTrue();
});

it('throws when SmugMug refuses a step', function () {
    FakeSmugMug::respondWith([GetAccessTokenRequest::class => MockResponse::make('oauth_problem=verifier_invalid', 401)]);
    expect(fn () => FakeSmugMug::connector()->oauth()->accessToken(new RequestToken('t', 's'), 'bad'))
        ->toThrow(UnauthorizedException::class, 'SmugMug responded 401 to GET /services/oauth/1.0a/getAccessToken: oauth_problem=verifier_invalid');

    FakeSmugMug::respondWith([GetRequestTokenRequest::class => MockResponse::make('oauth_problem=signature_invalid', 401)]);
    expect(fn () => FakeSmugMug::connector()->oauth()->requestToken())->toThrow(UnauthorizedException::class);

    FakeSmugMug::respondWith([GetRequestTokenRequest::class => MockResponse::make('nothing useful')]);
    expect(fn () => FakeSmugMug::connector()->oauth()->requestToken())->toThrow(InvalidArgumentException::class, 'needs its token and its secret');
});

it('needs the API secret to sign in', function () {
    expect(fn () => FakeSmugMug::connector(apiSecret: null)->oauth()->requestToken())->toThrow(LogicException::class, 'needs the API secret');
});

it('keeps a request token in the session and an access token out of logs', function () {
    $token = RequestToken::fromArray((new RequestToken('t', 's', true))->toArray());
    $access = new AccessToken('a', 'b');

    expect($token->toArray())->toBe(['token' => 't', 'secret' => 's', 'callbackConfirmed' => true])
        ->and(print_r($token, true))->not->toContain('=> s')
        ->and($token->__debugInfo()['secret'])->toBe('[redacted]')
        ->and($access->__debugInfo())->toBe(['token' => '[redacted]', 'secret' => '[redacted]'])
        ->and(fn () => new AccessToken('', 'b'))->toThrow(InvalidArgumentException::class)
        ->and(AccessToken::fromBody('oauth_token=x&oauth_token_secret=y')->secret)->toBe('y')
        ->and((new OAuth(FakeSmugMug::connector())))->toBeInstanceOf(OAuth::class);
});
