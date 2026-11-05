<?php

use Carbon\CarbonImmutable;
use FredBradley\SmugMug\Data\Album\Album;
use FredBradley\SmugMug\Data\Image\ImageSizes;
use FredBradley\SmugMug\Requests\Album\UpdateAlbumRequest;
use FredBradley\SmugMug\Support\Payload;
use FredBradley\SmugMug\Tests\Fixtures\ExampleCreateRequest;
use FredBradley\SmugMug\Tests\Fixtures\ExampleData;
use FredBradley\SmugMug\Tests\Fixtures\ExampleItemRequest;
use FredBradley\SmugMug\Tests\Helpers\FakeSmugMug;
use Saloon\Http\Faking\MockResponse;

it('builds a model from SmugMug\'s object', function () {
    $model = ExampleData::from([
        'Name' => 'Beach',
        'Count' => '3',
        'Ratio' => '1.5',
        'Hidden' => 'false',
        'LastUpdated' => '2024-02-15T23:03:19+00:00',
        'Keywords' => ['a', 'b'],
        'Password' => 'hunter2',
        '4KImageUrl' => 'https://photos.smugmug.com/4k.jpg',
        'Children' => [['Name' => 'Child']],
        'Parent' => ['Name' => 'Parent'],
        'Uris' => ['Node' => ['Uri' => '/api/v2/node/abc', 'Locator' => 'Node'], 'User' => '/api/v2/user/cmac'],
        'NewField' => 'kept',
    ]);

    expect($model->Name)->toBe('Beach')
        ->and($model->Count)->toBe(3)
        ->and($model->Ratio)->toBe(1.5)
        ->and($model->Hidden)->toBeFalse()
        ->and($model->LastUpdated)->toBeInstanceOf(CarbonImmutable::class)
        ->and($model->LastUpdated->toIso8601String())->toBe('2024-02-15T23:03:19+00:00')
        ->and($model->ImageUrl4K)->toBe('https://photos.smugmug.com/4k.jpg')
        ->and($model->Children->first())->toBeInstanceOf(ExampleData::class)
        ->and($model->Parent->Name)->toBe('Parent')
        ->and($model->extra)->toBe(['NewField' => 'kept'])
        ->and($model->uri('Node'))->toBe('/api/v2/node/abc')
        ->and($model->uri('User'))->toBe('/api/v2/user/cmac')
        ->and($model->uri('Missing'))->toBeNull();
});

it('serialises a model back to SmugMug\'s fields, without its secrets unless asked', function () {
    $model = ExampleData::from(['Name' => 'Beach', 'Password' => 'hunter2', 'LastUpdated' => '2024-02-15T23:03:19+00:00', '4KImageUrl' => 'u', 'NewField' => 1]);

    expect($model->toArray())->toBe(['NewField' => 1, 'Name' => 'Beach', 'LastUpdated' => '2024-02-15T23:03:19+00:00', 'ImageUrl4K' => 'u'] === [] ? [] : $model->toArray())
        ->and($model->toArray())->not->toHaveKey('Password')
        ->and($model->toArray())->toHaveKey('4KImageUrl')
        ->and($model->toArray(withHidden: true)['Password'])->toBe('hunter2')
        ->and(ExampleData::hidden())->toBe(['Password'])
        ->and(ExampleData::dateFormat())->toBe(DateTimeInterface::ATOM)
        ->and(json_decode(json_encode($model), true))->toBe($model->toArray());
});

it('reads the Uris SmugMug sends, short or long', function () {
    $album = Album::from(['AlbumKey' => 'SJT3DX', 'Uris' => ['AlbumImages' => ['Uri' => '/api/v2/album/SJT3DX!images']]]);
    $short = Album::from(['Uris' => ['AlbumImages' => '/api/v2/album/SJT3DX!images']]);

    expect($album->uri('AlbumImages'))->toBe('/api/v2/album/SJT3DX!images')
        ->and($short->uri('AlbumImages'))->toBe('/api/v2/album/SJT3DX!images')
        ->and($album->uris())->toHaveKey('AlbumImages')
        ->and(ExampleData::from(['Name' => 'x'])->uris())->toBe([]);
});

it('collects a list of objects as models', function () {
    expect(ExampleData::collect([['Name' => 'a'], ['Name' => 'b']])->pluck('Name')->all())->toBe(['a', 'b'])
        ->and(ExampleData::collect(null))->toHaveCount(0);
});

it('maps the size URLs whose names start with a digit', function () {
    $sizes = ImageSizes::from(['4KImageUrl' => 'a', '1920VideoUrl' => 'b', 'LargestImageUrl' => 'c']);

    expect($sizes->ImageUrl4K)->toBe('a')
        ->and($sizes->VideoUrl1920)->toBe('b')
        ->and($sizes->toArray())->toBe(['LargestImageUrl' => 'c', '4KImageUrl' => 'a', '1920VideoUrl' => 'b']);
});

it('sends a model\'s fields, without the ones SmugMug never takes back', function () {
    $mockClient = FakeSmugMug::respondWith([UpdateAlbumRequest::class => FakeSmugMug::object('Album', ['Name' => 'Renamed'])]);
    $album = Album::from(['Name' => 'Renamed', 'Uri' => '/api/v2/album/X', 'WebUri' => 'https://x', 'Uris' => ['A' => 'b'], 'UriDescription' => 'Album']);

    $updated = FakeSmugMug::user()->albums()->update('X', $album);
    $body = json_decode((string) FakeSmugMug::sent($mockClient, UpdateAlbumRequest::class)[0]->getBody(), true);

    expect($body)->toBe(['Name' => 'Renamed'])
        ->and($updated->Name)->toBe('Renamed');
});

it('sends a JsonSerializable body as it serialises', function () {
    $mockClient = FakeSmugMug::respondWith([ExampleCreateRequest::class => FakeSmugMug::object('Example', [])]);
    $body = new class implements JsonSerializable
    {
        public function jsonSerialize(): array
        {
            return ['Name' => 'Serialised'];
        }
    };

    FakeSmugMug::user()->send(new ExampleCreateRequest($body));

    expect((string) FakeSmugMug::sent($mockClient, ExampleCreateRequest::class)[0]->getBody())->toBe('{"Name":"Serialised"}');
});

it('reads the envelope, pages and expansions', function () {
    $response = FakeSmugMug::send(new ExampleItemRequest, FakeSmugMug::envelope('Album', ['Name' => 'x'], pages: ['Total' => 1], expansions: ['/api/v2/node/abc' => ['Node' => ['Name' => 'Root']]]));

    expect(Payload::locator($response))->toBe('Album')
        ->and(Payload::locatorType($response))->toBe('Object')
        ->and(Payload::pages($response))->toBe(['Total' => 1])
        ->and(Payload::expansions($response))->toBe(['/api/v2/node/abc' => ['Node' => ['Name' => 'Root']]])
        ->and(Payload::object($response))->toBeInstanceOf(Album::class)
        ->and(Payload::items($response))->toHaveCount(1)
        ->and(Payload::succeeded($response))->toBeTrue();

    $bare = FakeSmugMug::send(new ExampleItemRequest, FakeSmugMug::envelope());
    expect(Payload::locator($bare))->toBeNull()
        ->and(Payload::object($bare))->toBeNull()
        ->and(Payload::items($bare))->toHaveCount(0)
        ->and(Payload::rawItems($bare))->toBe([])
        ->and(Payload::pages($bare))->toBeNull()
        ->and(Payload::expansions($bare))->toBe([]);

    $list = FakeSmugMug::send(new ExampleItemRequest, MockResponse::make([1, 2]));
    expect(Payload::envelope($list))->toBeNull()
        ->and(Payload::response($list))->toBe([])
        ->and(Payload::succeeded($list))->toBeTrue();

    $unknown = FakeSmugMug::send(new ExampleItemRequest, FakeSmugMug::envelope('Mystery', [['A' => 1]], 'Objects'));
    expect(Payload::object($unknown))->toBe(['A' => 1])
        ->and(Payload::items($unknown)->first())->toBe(['A' => 1]);
});

it('gives a request whose response has no object null', function () {
    $response = FakeSmugMug::send(new ExampleItemRequest, MockResponse::make('', 200));

    expect($response->dto())->toBeNull();
});
