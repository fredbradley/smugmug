<?php

use FredBradley\SmugMug\Data\Album\Album;
use FredBradley\SmugMug\Exceptions\ForbiddenException;
use FredBradley\SmugMug\Exceptions\ServerErrorException;
use FredBradley\SmugMug\Listing;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\Album\GetAlbumImagesRequest;
use FredBradley\SmugMug\Requests\User\GetUserAlbumsRequest;
use FredBradley\SmugMug\SmugMugApiException;
use FredBradley\SmugMug\SmugMugConnector;
use FredBradley\SmugMug\Tests\Fixtures\ExampleData;
use FredBradley\SmugMug\Tests\Fixtures\ExampleListRequest;
use FredBradley\SmugMug\Tests\Fixtures\ExampleUntypedListRequest;
use FredBradley\SmugMug\Tests\Helpers\FakeSmugMug;
use Saloon\Http\Faking\MockResponse;
use Saloon\Http\PendingRequest;

/**
 * A fake list of $total items that answers each page by its start and count.
 */
function pagedList(int $total, string $locator = 'Example'): Closure
{
    return function (PendingRequest $request) use ($total, $locator) {
        $start = (int) ($request->query()->get('start') ?? 1);
        $count = (int) ($request->query()->get('count') ?? 10);
        $items = [];

        for ($i = $start; $i < $start + $count && $i <= $total; $i++) {
            $items[] = ['Name' => "item $i"];
        }

        return FakeSmugMug::objects($locator, $items, $total, $start, $count);
    };
}

it('pages through every item with start and count', function () {
    $mockClient = FakeSmugMug::respondWith([ExampleListRequest::class => pagedList(5)]);

    $names = FakeSmugMug::connector()->paginate(new ExampleListRequest, 2)->dtos()->map(fn (ExampleData $item) => $item->Name)->all();
    $queries = collect($mockClient->getRecordedResponses())->map(fn ($response) => $response->getPendingRequest()->query()->all())->all();

    expect($names)->toBe(['item 1', 'item 2', 'item 3', 'item 4', 'item 5'])
        ->and(array_map(fn ($query) => [$query['start'], $query['count']], $queries))->toBe([[1, 2], [3, 2], [5, 2]]);
});

it('stops when SmugMug gives no next page', function () {
    $mockClient = FakeSmugMug::respondWith([ExampleListRequest::class => FakeSmugMug::envelope('Example', [['Name' => 'a'], ['Name' => 'b']], 'Objects', ['Total' => 99, 'Start' => 1, 'Count' => 2])]);

    expect(FakeSmugMug::connector()->paginate(new ExampleListRequest, 2)->collect()->all())->toHaveCount(2);
    $mockClient->assertSentCount(1);
});

it('stops at a page that repeats the last', function () {
    $mockClient = FakeSmugMug::respondWith([ExampleListRequest::class => FakeSmugMug::objects('Example', [['Name' => 'a'], ['Name' => 'b']], 10)]);

    expect(FakeSmugMug::connector()->paginate(new ExampleListRequest, 2)->collect()->all())->toHaveCount(2);
    $mockClient->assertSentCount(2);
});

it('stops at an empty list, or once it has asked for every place up to the total', function () {
    FakeSmugMug::respondWith([ExampleListRequest::class => FakeSmugMug::objects('Example', [], 0)]);
    expect(FakeSmugMug::connector()->paginate(new ExampleListRequest)->collect()->all())->toBe([]);

    $mockClient = FakeSmugMug::respondWith([ExampleListRequest::class => pagedList(4)]);
    expect(FakeSmugMug::connector()->paginate(new ExampleListRequest, 2)->collect()->count())->toBe(4);
    $mockClient->assertSentCount(2);
});

it('carries on past short and empty pages while SmugMug says there is a next page, as it does for big albums', function () {
    // 10 places, of which SmugMug leaves out 3, 4, 5, 6 and 9: the pages of 2 are [1, 2], [], [7, 8], [10].
    $missing = [3, 4, 5, 6, 9];
    $mockClient = FakeSmugMug::respondWith([ExampleListRequest::class => function (PendingRequest $request) use ($missing) {
        $start = (int) $request->query()->get('start');
        $count = (int) $request->query()->get('count');
        $items = [];

        for ($i = $start; $i < $start + $count && $i <= 10; $i++) {
            if (! in_array($i, $missing, true)) {
                $items[] = ['Name' => "item $i"];
            }
        }

        $pages = ['Total' => 10, 'Start' => $start, 'Count' => count($items), 'RequestedCount' => $count];

        if ($start + $count <= 10) {
            $pages['NextPage'] = '/api/v2/example?start='.($start + $count).'&count='.$count;
        }

        return FakeSmugMug::envelope('Example', $items, 'Objects', $pages);
    }]);

    $names = FakeSmugMug::connector()->paginate(new ExampleListRequest, 2)->collect()->pluck('Name')->all();

    expect($names)->toBe(['item 1', 'item 2', 'item 7', 'item 8', 'item 10']);
    $mockClient->assertSentCount(5);
});

it('stops at an empty or short page when SmugMug gives no Pages', function () {
    $mockClient = FakeSmugMug::respondWith([ExampleListRequest::class => FakeSmugMug::envelope('Example', [['Name' => 'a']], 'Objects')]);

    expect(FakeSmugMug::connector()->paginate(new ExampleListRequest, 2)->collect()->all())->toHaveCount(1);
    $mockClient->assertSentCount(1);
});

it('maps items to the model the Locator names when the request has none', function () {
    FakeSmugMug::respondWith([ExampleUntypedListRequest::class => pagedList(3, 'Album')]);
    $albums = FakeSmugMug::connector()->paginate(new ExampleUntypedListRequest, 2)->dtos()->all();

    expect($albums)->toHaveCount(3)->and($albums[0])->toBeInstanceOf(Album::class);

    FakeSmugMug::respondWith([ExampleUntypedListRequest::class => pagedList(1, 'Unknown')]);
    expect(fn () => FakeSmugMug::connector()->paginate(new ExampleUntypedListRequest)->dtos()->all())->toThrow(LogicException::class, 'no model for Unknown');
});

it('throws for an error page part way through', function () {
    FakeSmugMug::respondWith([ExampleListRequest::class => function (PendingRequest $request) {
        return $request->query()->get('start') === 1 ? FakeSmugMug::objects('Example', [['Name' => 'a']], 5, 1, 1) : FakeSmugMug::failure(500, 'Broken');
    }]);
    expect(fn () => FakeSmugMug::connector()->paginate(new ExampleListRequest, 1)->collect()->all())->toThrow(SmugMugApiException::class, 'Broken');

    FakeSmugMug::respondWith([ExampleListRequest::class => MockResponse::make('<html>maintenance</html>')]);
    expect(fn () => FakeSmugMug::connector()->paginate(new ExampleListRequest)->collect()->all())->toThrow(ServerErrorException::class);
});

it('counts the pages from the total', function () {
    FakeSmugMug::respondWith([ExampleListRequest::class => pagedList(5)]);
    $paginator = FakeSmugMug::connector()->paginate(new ExampleListRequest, 2);

    expect($paginator->count())->toBe(3)
        ->and(fn () => FakeSmugMug::connector()->paginate(new ExampleListRequest, 0))->toThrow(LogicException::class);
});

it('gives a resource\'s list as a Listing: a page, every item, the first, the total', function () {
    $mockClient = FakeSmugMug::respondWith([GetAlbumImagesRequest::class => pagedList(5, 'AlbumImage')]);
    $images = FakeSmugMug::user()->albums()->images('SJT3DX', ['FileNames' => 'a.jpg']);

    expect($images)->toBeInstanceOf(Listing::class)
        ->and($images->get()->pluck('Title'))->toHaveCount(10 > 5 ? 5 : 10)
        ->and($images->page(2, 2)->count())->toBe(2)
        ->and($images->first()?->extra['Name'] ?? null)->toBe('item 1')
        ->and($images->total())->toBe(5)
        ->and($images->lazy(2)->count())->toBe(5)
        ->and($images->all(3)->count())->toBe(5)
        ->and(iterator_to_array($images))->toHaveCount(5)
        ->and($images->request()->query()->get('FileNames'))->toBe('a.jpg')
        ->and($images->paginator()->perPage())->toBe(100);

    $starts = collect($mockClient->getRecordedResponses())->map(fn ($response) => $response->getPendingRequest()->query()->get('start'))->all();
    expect($starts)->toContain(3); // page(2, 2)
});

it('throws for a failed page of a Listing', function () {
    FakeSmugMug::respondWith([GetUserAlbumsRequest::class => FakeSmugMug::failure(403, 'Forbidden')]);
    $albums = FakeSmugMug::user()->users()->albums('cmac');

    expect(fn () => $albums->get())->toThrow(ForbiddenException::class)
        ->and(fn () => $albums->total())->toThrow(ForbiddenException::class);

    FakeSmugMug::respondWith([GetUserAlbumsRequest::class => MockResponse::make('not json')]);
    expect(fn () => $albums->get())->toThrow(ServerErrorException::class)
        ->and(fn () => $albums->total())->toThrow(ServerErrorException::class);

    FakeSmugMug::respondWith([GetUserAlbumsRequest::class => FakeSmugMug::envelope('Album', [['Name' => 'a']], 'Objects')]);
    expect($albums->total())->toBe(1);
});

it('sends the resource\'s options with every request', function () {
    $mockClient = FakeSmugMug::respondWith([GetUserAlbumsRequest::class => FakeSmugMug::objects('Album', [])]);

    FakeSmugMug::user()->users()->expand('HighlightImage')->filter('Name', 'UrlPath')->filterUri('AlbumImages')
        ->albums('cmac', Options::make()->expand('Node')->with('Order', 'Date Added (Descending)'))->get();

    expect(FakeSmugMug::received($mockClient, GetUserAlbumsRequest::class)->query()->all())->toBe([
        '_expand' => 'HighlightImage,Node',
        '_filter' => 'Name,UrlPath',
        '_filteruri' => 'AlbumImages',
        'Order' => 'Date Added (Descending)',
    ])->and(FakeSmugMug::user()->users()->connector())->toBeInstanceOf(SmugMugConnector::class);
});
