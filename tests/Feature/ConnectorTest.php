<?php

use FredBradley\SmugMug\Auth\OAuth1Authenticator;
use FredBradley\SmugMug\Data\Album\Album;
use FredBradley\SmugMug\Exceptions\NotFoundException;
use FredBradley\SmugMug\Requests\AdhocBodyRequest;
use FredBradley\SmugMug\Requests\AdhocRequest;
use FredBradley\SmugMug\Requests\User\GetAuthUserRequest;
use FredBradley\SmugMug\Requests\User\UpdateAuthUserRequest;
use FredBradley\SmugMug\SmugMugConnector;
use FredBradley\SmugMug\SmugMugPaginator;
use FredBradley\SmugMug\Tests\Fixtures\ExampleCreateRequest;
use FredBradley\SmugMug\Tests\Fixtures\ExampleElsewhereRequest;
use FredBradley\SmugMug\Tests\Fixtures\ExampleItemRequest;
use FredBradley\SmugMug\Tests\Fixtures\ExampleListRequest;
use FredBradley\SmugMug\Tests\Helpers\FakeSmugMug;
use Saloon\Config;
use Saloon\Enums\Method;
use Saloon\Exceptions\Request\FatalRequestException;
use Saloon\Exceptions\Request\RequestException;
use Saloon\Http\Auth\QueryAuthenticator;
use Saloon\Http\Faking\MockResponse;

it('reads public data with the API key alone', function () {
    $mockClient = FakeSmugMug::respondWith([ExampleItemRequest::class => FakeSmugMug::object('Example', ['Name' => 'x'])]);

    FakeSmugMug::connector()->send(new ExampleItemRequest('K1'));
    $sent = FakeSmugMug::sent($mockClient, ExampleItemRequest::class)[0];

    expect((string) $sent->getUri())->toBe('https://api.smugmug.com/api/v2/example/K1?APIKey=test-api-key')
        ->and($sent->getHeaderLine('Accept'))->toBe('application/json')
        ->and($sent->hasHeader('Authorization'))->toBeFalse()
        ->and(FakeSmugMug::connector()->getAuthenticator())->toBeInstanceOf(QueryAuthenticator::class)
        ->and(FakeSmugMug::connector()->hasToken())->toBeFalse()
        ->and(FakeSmugMug::connector()->apiKey())->toBe('test-api-key');
});

it('signs every request with OAuth 1.0a when it has a token', function () {
    $mockClient = FakeSmugMug::respondWith([ExampleItemRequest::class => FakeSmugMug::object('Example', ['Name' => 'x'])]);

    FakeSmugMug::user()->send(new ExampleItemRequest('K1', ['_expand' => 'HighlightImage', 'Text' => 'a b+c']));
    $sent = FakeSmugMug::sent($mockClient, ExampleItemRequest::class)[0];
    $oauth = FakeSmugMug::oauthParameters($sent);

    expect((string) $sent->getUri())->toBe('https://api.smugmug.com/api/v2/example/K1?_expand=HighlightImage&Text=a%20b%2Bc')
        ->and($sent->getHeaderLine('Authorization'))->toStartWith('OAuth oauth_consumer_key="test-api-key", oauth_nonce="')
        ->and($oauth)->toHaveKeys(['oauth_consumer_key', 'oauth_nonce', 'oauth_signature_method', 'oauth_timestamp', 'oauth_version', 'oauth_token', 'oauth_signature'])
        ->and($oauth['oauth_token'])->toBe('user-token')
        ->and($oauth['oauth_signature_method'])->toBe('HMAC-SHA1')
        ->and($oauth['oauth_version'])->toBe('1.0')
        ->and((int) $oauth['oauth_timestamp'])->toBeGreaterThan(time() - 60)
        ->and(FakeSmugMug::signatureIsValid($sent))->toBeTrue()
        ->and(FakeSmugMug::signatureIsValid($sent, tokenSecret: 'wrong'))->toBeFalse()
        ->and(FakeSmugMug::user()->getAuthenticator())->toBeInstanceOf(OAuth1Authenticator::class);
});

it('signs a JSON body without its fields, as OAuth leaves them out', function () {
    $mockClient = FakeSmugMug::respondWith([ExampleCreateRequest::class => FakeSmugMug::object('Example', ['Name' => 'x'])]);

    FakeSmugMug::user()->send(new ExampleCreateRequest(['Name' => 'New']));
    $sent = FakeSmugMug::sent($mockClient, ExampleCreateRequest::class)[0];

    expect((string) $sent->getBody())->toBe('{"Name":"New"}')
        ->and($sent->getHeaderLine('Content-Type'))->toBe('application/json')
        ->and(FakeSmugMug::signatureIsValid($sent))->toBeTrue();
});

it('gives each request a new nonce', function () {
    $mockClient = FakeSmugMug::respondWith([ExampleItemRequest::class => FakeSmugMug::object('Example', [])]);

    FakeSmugMug::user()->send(new ExampleItemRequest);
    FakeSmugMug::user()->send(new ExampleItemRequest);
    $nonces = array_map(fn ($request) => FakeSmugMug::oauthParameters($request)['oauth_nonce'], FakeSmugMug::sent($mockClient, ExampleItemRequest::class));

    expect($nonces)->toHaveCount(2)->and($nonces[0])->not->toBe($nonces[1]);
});

it('acts as another user, or as nobody', function () {
    $connector = FakeSmugMug::connector()->withToken('other-token', 'other-secret');

    expect($connector->hasToken())->toBeTrue()
        ->and($connector->withoutToken()->hasToken())->toBeFalse()
        ->and($connector->tokenFingerprint())->not->toBe(FakeSmugMug::user()->tokenFingerprint())
        ->and(FakeSmugMug::user()->tokenFingerprint())->toBe(FakeSmugMug::user()->tokenFingerprint());
});

it('refuses to ask who the user is without a token, rather than pass on SmugMug\'s 404', function () {
    $mockClient = FakeSmugMug::respondWith([
        GetAuthUserRequest::class => FakeSmugMug::object('User', ['NickName' => 'cmac']),
        UpdateAuthUserRequest::class => FakeSmugMug::object('User', ['NickName' => 'cmac']),
    ]);

    expect(fn () => FakeSmugMug::connector()->users()->me())->toThrow(LogicException::class, 'needs an access token')
        ->and(fn () => FakeSmugMug::connector()->users()->updateMe(['NickName' => 'x']))->toThrow(LogicException::class, 'needs an access token')
        ->and($mockClient->getRecordedResponses())->toBe([])
        ->and(FakeSmugMug::user()->users()->me()->NickName)->toBe('cmac')
        ->and(FakeSmugMug::user()->users()->updateMe(['NickName' => 'x'])->NickName)->toBe('cmac');
});

it('refuses to be built without what it needs', function () {
    expect(fn () => new SmugMugConnector(''))->toThrow(InvalidArgumentException::class, 'needs an API key')
        ->and(fn () => new SmugMugConnector('key', null, 'token', 'secret'))->toThrow(InvalidArgumentException::class, 'needs the API secret')
        ->and(fn () => new SmugMugConnector('key', 'secret', 'token', null))->toThrow(InvalidArgumentException::class, 'access token secret');
});

it('never shows or serialises its secrets', function () {
    $connector = FakeSmugMug::user();

    expect(print_r($connector, true))->not->toContain('user-token-secret')->not->toContain('test-api-secret')
        ->and($connector->__debugInfo())->toBe(['apiKey' => 'test-api-key', 'apiSecret' => '[redacted]', 'accessToken' => '[redacted]', 'accessTokenSecret' => '[redacted]'])
        ->and(FakeSmugMug::connector(apiSecret: null)->__debugInfo()['apiSecret'])->toBeNull()
        ->and(fn () => serialize($connector))->toThrow(LogicException::class, 'cannot be serialised');
});

it('sends requests only to SmugMug', function () {
    FakeSmugMug::respondWith([ExampleElsewhereRequest::class => MockResponse::make([])]);

    expect(fn () => FakeSmugMug::user()->send(new ExampleElsewhereRequest))->toThrow(LogicException::class, 'only sends requests to https://api.smugmug.com')
        ->and(fn () => FakeSmugMug::user()->send(new ExampleElsewhereRequest('http://api.smugmug.com/api/v2')))->toThrow(LogicException::class);
});

it('fetches any URI SmugMug gave, as its model', function () {
    $mockClient = FakeSmugMug::respondWith([
        AdhocRequest::class => fn ($pending) => str_contains($pending->getUrl(), '!images')
            ? FakeSmugMug::objects('AlbumImage', [['ImageKey' => 'a'], ['ImageKey' => 'b']])
            : FakeSmugMug::object('Album', ['AlbumKey' => 'SJT3DX']),
    ]);

    $album = FakeSmugMug::user()->follow('/api/v2/album/SJT3DX');
    $images = FakeSmugMug::user()->follow('https://api.smugmug.com/api/v2/album/SJT3DX!images?count=2', ['start' => 3]);

    expect($album)->toBeInstanceOf(Album::class)->and($album->AlbumKey)->toBe('SJT3DX')
        ->and($images)->toHaveCount(2)->and($images->first()->ImageKey)->toBe('a')
        ->and(FakeSmugMug::urisReceived($mockClient, AdhocRequest::class)[1])->toBe('https://api.smugmug.com/api/v2/album/SJT3DX!images?count=2&start=3')
        ->and(fn () => FakeSmugMug::user()->follow('https://evil.example.test/api/v2/album/x'))->toThrow(InvalidArgumentException::class);

    FakeSmugMug::respondWith([AdhocRequest::class => FakeSmugMug::failure(404, 'Not Found')]);
    expect(fn () => FakeSmugMug::user()->follow('album/nope'))->toThrow(NotFoundException::class);
});

it('sends any method to any path', function () {
    $mockClient = FakeSmugMug::respondWith([AdhocRequest::class => FakeSmugMug::action(), AdhocBodyRequest::class => FakeSmugMug::action()]);
    $connector = FakeSmugMug::user();

    $connector->get('album/SJT3DX', ['_verbosity' => 1]);
    $connector->delete('/api/v2/album/SJT3DX');
    $connector->post('/api/v2/album/SJT3DX!moveimages', ['MoveUris' => 'x']);
    $connector->patch('/api/v2/album/SJT3DX', ['Name' => 'Renamed']);
    $connector->put('/api/v2/album/SJT3DX!applyalbumtemplate', ['AlbumTemplateUri' => '/api/v2/albumtemplate/1']);

    $methods = collect($mockClient->getRecordedResponses())->map(fn ($response) => $response->getPsrRequest()->getMethod().' '.$response->getPsrRequest()->getUri()->getPath())->all();

    expect($methods)->toBe([
        'GET /api/v2/album/SJT3DX',
        'DELETE /api/v2/album/SJT3DX',
        'POST /api/v2/album/SJT3DX!moveimages',
        'PATCH /api/v2/album/SJT3DX',
        'PUT /api/v2/album/SJT3DX!applyalbumtemplate',
    ])->and((string) FakeSmugMug::sent($mockClient, AdhocBodyRequest::class)[1]->getBody())->toBe('{"Name":"Renamed"}');
});

it('takes its timeouts and retries from config', function () {
    config()->set('smugmug.timeouts', ['connect' => 3, 'request' => 7]);
    config()->set('smugmug.retries', ['times' => 3, 'interval_ms' => 1]);
    $connector = FakeSmugMug::connector();

    expect($connector->getConnectTimeout())->toBe(3.0)
        ->and($connector->getRequestTimeout())->toBe(7.0)
        ->and($connector->tries)->toBe(3)
        ->and($connector->retryInterval)->toBe(1)
        ->and($connector->useExponentialBackoff)->toBeTrue();

    config()->set('smugmug.timeouts', ['connect' => 0, 'request' => 'soon']);
    config()->set('smugmug.retries.times', null);
    $connector = FakeSmugMug::connector();

    expect($connector->getConnectTimeout())->toBe((float) Config::$defaultConnectionTimeout)
        ->and($connector->getRequestTimeout())->toBe((float) Config::$defaultRequestTimeout)
        ->and($connector->tries)->toBeNull();
});

it('retries only a GET, and only for a fault on SmugMug\'s side or its rate limit', function () {
    config()->set('smugmug.retries', ['times' => 3, 'interval_ms' => 1]);
    $attempts = 0;
    FakeSmugMug::respondWith([ExampleItemRequest::class => function () use (&$attempts) {
        $attempts++;

        return $attempts < 3 ? FakeSmugMug::failure(503, 'Busy') : FakeSmugMug::object('Example', ['Name' => 'x']);
    }, ExampleCreateRequest::class => FakeSmugMug::failure(503, 'Busy')]);

    expect(FakeSmugMug::connector()->send(new ExampleItemRequest)->status())->toBe(200)
        ->and($attempts)->toBe(3);

    $connector = FakeSmugMug::connector();
    $response = $connector->send(new ExampleCreateRequest(['Name' => 'x']));
    expect($response->status())->toBe(503);

    $failure = fn (int $status) => new RequestException(FakeSmugMug::send(new ExampleItemRequest, FakeSmugMug::failure($status)));

    expect($connector->handleRetry($failure(429), new ExampleItemRequest))->toBeTrue()
        ->and($connector->handleRetry($failure(500), new ExampleItemRequest))->toBeTrue()
        ->and($connector->handleRetry($failure(404), new ExampleItemRequest))->toBeFalse()
        ->and($connector->handleRetry($failure(500), new ExampleCreateRequest))->toBeFalse()
        ->and($connector->handleRetry(new FatalRequestException(new Exception('down'), $connector->createPendingRequest(new ExampleItemRequest)), new ExampleItemRequest))->toBeTrue()
        ->and($connector->handleRetry($failure(500), new AdhocRequest(Method::GET, 'album/x')))->toBeTrue();
});

it('pages through a list request', function () {
    $paginator = FakeSmugMug::connector()->paginate(new ExampleListRequest, 25);

    expect($paginator)->toBeInstanceOf(SmugMugPaginator::class)
        ->and($paginator->perPage())->toBe(25)
        ->and(FakeSmugMug::connector()->paginate(new ExampleListRequest)->perPage())->toBe(100);
});

it('reads the rate limit from a response', function () {
    $response = FakeSmugMug::send(new ExampleItemRequest, FakeSmugMug::envelope(headers: ['X-RateLimit-Remaining' => '0', 'X-RateLimit-Reset' => (string) (time() + 30)]));
    $limit = FakeSmugMug::connector()->rateLimit($response);

    expect($limit->remaining)->toBe(0)
        ->and($limit->exhausted())->toBeTrue()
        ->and($limit->resetsAt?->getTimestamp())->toBeGreaterThan(time())
        ->and($limit->retryAfter)->toBeNull();
});
