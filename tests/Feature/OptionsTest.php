<?php

use FredBradley\SmugMug\Enums\Access;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Tests\Fixtures\ExampleItemRequest;
use FredBradley\SmugMug\Tests\Helpers\FakeSmugMug;

it('writes SmugMug\'s options for the query string', function () {
    $options = Options::make()
        ->expand('HighlightImage', 'Node.ChildNodes')
        ->expand('HighlightImage')
        ->filter('Name', 'UrlPath')
        ->filterUri('AlbumImages')
        ->shortUris()
        ->verbosity(1)
        ->config(['expand' => ['ChildNodes' => ['args' => ['count' => 25]]]])
        ->start(11)
        ->count(10)
        ->with('Text', 'beach')
        ->with('Relevance', true)
        ->with('Keywords', ['sea', 'sand'])
        ->with('Access', Access::Full)
        ->withMany(['Ratio' => 1.5, 'Gone' => null]);

    expect($options->toArray())->toBe([
        '_expand' => 'HighlightImage,Node.ChildNodes',
        '_filter' => 'Name,UrlPath',
        '_filteruri' => 'AlbumImages',
        '_shorturis' => '',
        '_verbosity' => 1,
        '_config' => '{"expand":{"ChildNodes":{"args":{"count":25}}}}',
        'start' => 11,
        'count' => 10,
        'Text' => 'beach',
        'Relevance' => 'true',
        'Keywords' => 'sea,sand',
        'Access' => 'Full',
        'Ratio' => 1.5,
    ])->and($options->get('Text'))->toBe('beach')
        ->and($options->get('Nothing'))->toBeNull()
        ->and($options->parameters()['Keywords'])->toBe(['sea', 'sand']);
});

it('is immutable', function () {
    $options = Options::make(['a' => 1]);
    $changed = $options->with('b', 2)->with('a', null);

    expect($options->toArray())->toBe(['a' => 1])
        ->and($changed->toArray())->toBe(['b' => 2]);
});

it('drops every Uri with an empty filterUri, and pages by number', function () {
    expect(Options::make()->filterUri()->toArray())->toBe(['_filteruri' => ''])
        ->and(Options::make()->page(3, 50)->toArray())->toBe(['count' => 50, 'start' => 101])
        ->and(Options::make()->page(1, 20)->toArray())->toBe(['count' => 20, 'start' => 1]);
});

it('merges options, combining the names to expand and filter', function () {
    $merged = Options::make(['_expand' => 'User'])->expand('Node')->filter('Name')->with('count', 5)
        ->merge(Options::make()->expand('Album')->filter('Name', 'Title')->with('count', 9))
        ->merge(['Text' => 'x']);

    expect($merged->toArray())->toBe(['_expand' => 'User,Node,Album', '_filter' => 'Name,Title', 'count' => 9, 'Text' => 'x'])
        ->and(Options::of(null)->toArray())->toBe([])
        ->and(Options::of(['a' => 1])->toArray())->toBe(['a' => 1]);
});

it('refuses options SmugMug would refuse', function () {
    expect(fn () => Options::make()->verbosity(4))->toThrow(InvalidArgumentException::class)
        ->and(fn () => Options::make()->start(0))->toThrow(InvalidArgumentException::class)
        ->and(fn () => Options::make()->count(0))->toThrow(InvalidArgumentException::class)
        ->and(fn () => Options::make()->config(['bad' => NAN]))->toThrow(InvalidArgumentException::class, 'JSON-encodable');
});

it('sends the options as the request\'s query, encoded as RFC 3986', function () {
    $mockClient = FakeSmugMug::respondWith([ExampleItemRequest::class => FakeSmugMug::object('Example', [])]);

    FakeSmugMug::connector()->send(new ExampleItemRequest('K1', Options::make()->expand('Node')->with('Text', 'big cats')));

    expect(FakeSmugMug::urisReceived($mockClient, ExampleItemRequest::class)[0])->toBe('https://api.smugmug.com/api/v2/example/K1?_expand=Node&Text=big%20cats&APIKey=test-api-key');
});
