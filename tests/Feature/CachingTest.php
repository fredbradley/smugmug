<?php

use FredBradley\SmugMug\Tests\Fixtures\ExampleCreateRequest;
use FredBradley\SmugMug\Tests\Fixtures\ExampleItemRequest;
use FredBradley\SmugMug\Tests\Helpers\FakeSmugMug;
use Saloon\Http\Faking\MockResponse;

beforeEach(fn () => config()->set('smugmug.cache', ['enabled' => true, 'store' => 'array', 'seconds' => 60]));

it('serves a repeated GET from the cache', function () {
    $mockClient = FakeSmugMug::respondWith([ExampleItemRequest::class => FakeSmugMug::object('Example', ['Name' => 'x'])]);

    $first = FakeSmugMug::user()->send(new ExampleItemRequest('K1'));
    $second = FakeSmugMug::user()->send(new ExampleItemRequest('K1'));

    expect($first->isCached())->toBeFalse()
        ->and($second->isCached())->toBeTrue()
        ->and($second->dto()->Name)->toBe('x')
        ->and((new ExampleItemRequest)->cacheExpiryInSeconds())->toBe(60);
});

it('keeps each user\'s answers apart', function () {
    FakeSmugMug::respondWith([ExampleItemRequest::class => FakeSmugMug::object('Example', ['Name' => 'x'])]);

    FakeSmugMug::user()->send(new ExampleItemRequest('K1'));

    expect(FakeSmugMug::connector('someone-else', 'their-secret')->send(new ExampleItemRequest('K1'))->isCached())->toBeFalse()
        ->and(FakeSmugMug::connector()->send(new ExampleItemRequest('K1'))->isCached())->toBeFalse()
        ->and(FakeSmugMug::user()->send(new ExampleItemRequest('K2'))->isCached())->toBeFalse();
});

it('does not keep an answer whose envelope says it failed', function () {
    FakeSmugMug::respondWith([ExampleItemRequest::class => MockResponse::make(['Code' => 500, 'Message' => 'Oops'])]);

    FakeSmugMug::user()->send(new ExampleItemRequest('K1'));

    expect(FakeSmugMug::user()->send(new ExampleItemRequest('K1'))->isCached())->toBeFalse();

    FakeSmugMug::respondWith([ExampleItemRequest::class => MockResponse::make('not json')]);
    FakeSmugMug::user()->send(new ExampleItemRequest('K3'));
    expect(FakeSmugMug::user()->send(new ExampleItemRequest('K3'))->isCached())->toBeFalse();
});

it('never caches a write, or anything while the cache is off', function () {
    FakeSmugMug::respondWith([ExampleCreateRequest::class => FakeSmugMug::object('Example', []), ExampleItemRequest::class => FakeSmugMug::object('Example', [])]);

    FakeSmugMug::user()->send(new ExampleCreateRequest(['Name' => 'x']));
    expect(FakeSmugMug::user()->send(new ExampleCreateRequest(['Name' => 'x']))->isCached())->toBeFalse();

    config()->set('smugmug.cache.enabled', false);
    FakeSmugMug::user()->send(new ExampleItemRequest('K9'));
    expect(FakeSmugMug::user()->send(new ExampleItemRequest('K9'))->isCached())->toBeFalse();
});
