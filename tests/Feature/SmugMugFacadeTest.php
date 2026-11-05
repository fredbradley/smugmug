<?php

use FredBradley\SmugMug\Data\User\User;
use FredBradley\SmugMug\Facades\SmugMug;
use FredBradley\SmugMug\Requests\User\GetAuthUserRequest;
use FredBradley\SmugMug\SmugMugConnector;
use FredBradley\SmugMug\Tests\Helpers\FakeSmugMug;

it('resolves the default connection from config', function () {
    expect(SmugMug::connector())->toBeInstanceOf(SmugMugConnector::class)
        ->and(SmugMug::connector()->apiKey())->toBe('test-api-key')
        ->and(SmugMug::hasToken())->toBeFalse();
});

it('signs for the account in config', function () {
    config()->set('smugmug.access_token', 'configured-token');
    config()->set('smugmug.access_token_secret', 'configured-secret');
    $mockClient = FakeSmugMug::respondWith([GetAuthUserRequest::class => FakeSmugMug::object('User', ['NickName' => 'cmac'])]);

    $me = SmugMug::users()->me();

    expect($me)->toBeInstanceOf(User::class)
        ->and($me->NickName)->toBe('cmac')
        ->and(FakeSmugMug::oauthParameters(FakeSmugMug::sent($mockClient, GetAuthUserRequest::class)[0])['oauth_token'])->toBe('configured-token');
});

it('acts for one of the users of the application', function () {
    expect(SmugMug::withToken('t', 's')->hasToken())->toBeTrue();
});

it('resolves a named connection', function () {
    config()->set('smugmug.connections.studio', ['api_key' => 'studio-key', 'api_secret' => 'studio-secret', 'access_token' => 'tok', 'access_token_secret' => 'sec']);

    expect(SmugMug::connection('studio')->apiKey())->toBe('studio-key')
        ->and(SmugMug::connection('studio'))->toBe(SmugMug::connection('studio'))
        ->and(SmugMug::connection('studio')->hasToken())->toBeTrue()
        ->and(fn () => SmugMug::connection('missing'))->toThrow(InvalidArgumentException::class, 'No SmugMug connection named "missing"');
});

it('says what a connection is missing', function () {
    config()->set('smugmug.connections.empty', ['api_key' => '']);
    config()->set('smugmug.connections.half', ['api_key' => 'k', 'access_token' => 't']);

    expect(fn () => SmugMug::connection('empty'))->toThrow(InvalidArgumentException::class, 'needs an api_key')
        ->and(fn () => SmugMug::connection('half'))->toThrow(InvalidArgumentException::class, 'needs the api_secret and the access_token_secret');
});

it('refuses a method the connector does not have', function () {
    expect(fn () => SmugMug::nothingHere())->toThrow(BadMethodCallException::class);
});
