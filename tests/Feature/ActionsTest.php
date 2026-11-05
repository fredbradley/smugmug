<?php

use FredBradley\SmugMug\Data\Upload\UploadResult;
use FredBradley\SmugMug\Enums\Size;
use FredBradley\SmugMug\Requests\Album\MoveAlbumImagesRequest;
use FredBradley\SmugMug\Requests\Image\GetImageSizeRequest;
use FredBradley\SmugMug\Requests\Node\MoveNodesRequest;
use FredBradley\SmugMug\Requests\OAuth\GetAccessTokenRequest;
use FredBradley\SmugMug\Requests\OAuth\GetRequestTokenRequest;
use FredBradley\SmugMug\Tests\Fixtures\ExampleActionRequest;
use FredBradley\SmugMug\Tests\Helpers\FakeSmugMug;
use FredBradley\SmugMug\Upload\Media;

it('joins a list of URIs with commas where SmugMug takes one string', function () {
    $mockClient = FakeSmugMug::respondWith([MoveAlbumImagesRequest::class => FakeSmugMug::action(['AsyncStatusUri' => '/api/v2/status/x'])]);

    $result = FakeSmugMug::user()->albums()->moveImages('SJT3DX', [
        'MoveUris' => ['/api/v2/album/A/image/a-0', '/api/v2/album/A/image/b-0'],
        'Async' => true,
    ]);

    expect($result['AsyncStatusUri'])->toBe('/api/v2/status/x')
        ->and(json_decode((string) FakeSmugMug::sent($mockClient, MoveAlbumImagesRequest::class)[0]->getBody(), true))
        ->toBe(['MoveUris' => '/api/v2/album/A/image/a-0,/api/v2/album/A/image/b-0', 'Async' => true]);
});

it('keeps a list where SmugMug takes an array', function () {
    $mockClient = FakeSmugMug::respondWith([MoveNodesRequest::class => FakeSmugMug::action()]);

    FakeSmugMug::user()->nodes()->move('XWx8t', ['MoveUris' => ['/api/v2/node/a', '/api/v2/node/b']]);

    expect(json_decode((string) FakeSmugMug::sent($mockClient, MoveNodesRequest::class)[0]->getBody(), true))
        ->toBe(['MoveUris' => ['/api/v2/node/a', '/api/v2/node/b']]);
});

it('leaves a comma-separated string as it is', function () {
    $mockClient = FakeSmugMug::respondWith([ExampleActionRequest::class => FakeSmugMug::action()]);

    FakeSmugMug::user()->send(new ExampleActionRequest(['MoveUris' => 'a,b']));

    expect((string) FakeSmugMug::sent($mockClient, ExampleActionRequest::class)[0]->getBody())->toBe('{"MoveUris":"a,b"}');
});

it('names each size of an image', function () {
    expect(array_map(fn (Size $size) => (new GetImageSizeRequest('k-0', $size))->resolveEndpoint(), Size::cases()))->toBe([
        '/api/v2/image/k-0!sizetiny', '/api/v2/image/k-0!sizethumb', '/api/v2/image/k-0!sizesmall', '/api/v2/image/k-0!sizemedium',
        '/api/v2/image/k-0!sizelarge', '/api/v2/image/k-0!sizexlarge', '/api/v2/image/k-0!sizex2large', '/api/v2/image/k-0!sizex3large',
        '/api/v2/image/k-0!sizex4large', '/api/v2/image/k-0!sizex5large', '/api/v2/image/k-0!size4k', '/api/v2/image/k-0!size5k',
        '/api/v2/image/k-0!sizeoriginal',
    ]);
});

it('never retries a step of the OAuth flow', function () {
    expect((new GetRequestTokenRequest)->isSafe())->toBeFalse()
        ->and((new GetAccessTokenRequest('123'))->isSafe())->toBeFalse()
        ->and((new GetRequestTokenRequest)->callback)->toBe('oob');
});

it('reads the key of an uploaded image, when there is one', function () {
    expect((new UploadResult(ImageUri: '/api/v2/image/Ab12-0'))->imageKey())->toBe('Ab12-0')
        ->and((new UploadResult)->imageKey())->toBeNull()
        ->and((new UploadResult(ImageUri: '/api/v2/album/X'))->imageKey())->toBeNull();
});

it('knows the types of the files SmugMug takes', function (string $name, string $type) {
    expect(Media::fromContents('x', $name)->mimeType)->toBe($type);
})->with([
    ['a.JPEG', 'image/jpeg'], ['a.png', 'image/png'], ['a.gif', 'image/gif'], ['a.heif', 'image/heif'], ['a.webp', 'image/webp'],
    ['a.tif', 'image/tiff'], ['a.mov', 'video/quicktime'], ['a.m4v', 'video/x-m4v'], ['a.avi', 'video/x-msvideo'], ['a.mts', 'video/mp2t'],
]);

it('takes the type of a file on disk from its contents, then its name', function () {
    $png = tempnam(sys_get_temp_dir(), 'smug');
    file_put_contents($png, base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII='));
    $unknown = tempnam(sys_get_temp_dir(), 'smug').'.jpg';
    file_put_contents($unknown, "\x00\x01");

    expect(Media::fromPath($png)->mimeType)->toBe('image/png')
        ->and(Media::fromPath($unknown)->mimeType)->toBe('image/jpeg')
        ->and(Media::fromPath($png, mimeType: 'image/x-custom')->mimeType)->toBe('image/x-custom')
        ->and(Media::fromContents('x', 'a.jpg')->contentMd5())->toBe(base64_encode(md5('x', true)));

    unlink($png);
    unlink($unknown);
});
