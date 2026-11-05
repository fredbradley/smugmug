<?php

use FredBradley\SmugMug\Data\Album\Album;
use FredBradley\SmugMug\Listing;
use FredBradley\SmugMug\Requests\Album\GetAlbumRequest;
use FredBradley\SmugMug\Requests\AlbumImage\GetAlbumImageRequest;
use FredBradley\SmugMug\Requests\Folder\GetFolderRequest;
use FredBradley\SmugMug\Requests\Image\GetImageRequest;
use FredBradley\SmugMug\Requests\Node\GetNodeRequest;
use FredBradley\SmugMug\Requests\User\GetUserRequest;
use FredBradley\SmugMug\Resources\BaseResource;
use FredBradley\SmugMug\Tests\Helpers\FakeSmugMug;
use Illuminate\Support\Collection;

$resources = require __DIR__.'/resources.php';

it('sends its request and returns what the request maps to', function (string $accessor, string $method, array $args, string $requestClass, string $kind, ?string $dto) {
    $sample = ['Uri' => '/api/v2/example/1', 'Name' => 'x'];
    $locator = FakeSmugMug::locatorFor($dto);
    $mockClient = FakeSmugMug::respondWith([$requestClass => match ($kind) {
        'list' => FakeSmugMug::objects($locator, [$sample]),
        'item', 'create', 'update' => FakeSmugMug::object($locator, $sample),
        'action', 'deleteaction' => FakeSmugMug::action(['Done' => true]),
        default => FakeSmugMug::envelope(),
    }]);

    $result = FakeSmugMug::user()->{$accessor}()->{$method}(...$args);

    if ($kind === 'list') {
        expect($result)->toBeInstanceOf(Listing::class);
        $result = $result->get();
        expect($result)->toBeInstanceOf(Collection::class)->toHaveCount(1)->and($result->first())->toBeInstanceOf($dto);
    }

    match ($kind) {
        'item', 'create', 'update' => expect($result)->toBeInstanceOf($dto ?? Album::class),
        'delete' => expect($result)->toBeTrue(),
        'action', 'deleteaction' => expect($result)->toBe(['Uri' => '/api/v2/example', 'Done' => true]),
        default => null,
    };

    $mockClient->assertSent($requestClass);

    if (in_array($kind, ['create', 'update'], true)) {
        expect(json_decode((string) FakeSmugMug::sent($mockClient, $requestClass)[0]->getBody(), true))->toBe(['Name' => 'x']);
    }

    if (in_array($kind, ['action', 'deleteaction'], true)) {
        expect(json_decode((string) FakeSmugMug::sent($mockClient, $requestClass)[0]->getBody(), true))->toBe(['Async' => true]);
    }
})->with($resources);

it('finds an object, or null when SmugMug has none', function (string $accessor, array $args, string $requestClass) {
    FakeSmugMug::respondWith([$requestClass => FakeSmugMug::failure(404, 'Not Found')]);

    expect(FakeSmugMug::user()->{$accessor}()->find(...$args))->toBeNull();
})->with([
    'users' => ['users', ['cmac'], GetUserRequest::class],
    'albums' => ['albums', ['SJT3DX'], GetAlbumRequest::class],
    'albumImages' => ['albumImages', ['SJT3DX', 'jPPKD2c-1'], GetAlbumImageRequest::class],
    'images' => ['images', ['jPPKD2c-1'], GetImageRequest::class],
    'nodes' => ['nodes', ['XWx8t'], GetNodeRequest::class],
    'folders' => ['folders', ['cmac', 'Travel'], GetFolderRequest::class],
]);

it('reaches every resource from the connector', function () use ($resources) {
    $accessors = collect($resources)->map(fn (array $row) => $row[0])->unique()->values();

    foreach ([...$accessors, 'uploads'] as $accessor) {
        expect(FakeSmugMug::connector()->{$accessor}())->toBeInstanceOf(BaseResource::class);
    }

    expect($accessors)->toHaveCount(26);
});
