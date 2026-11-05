<?php

use FredBradley\SmugMug\Data\Album\AlbumImage;
use FredBradley\SmugMug\Data\Upload\UploadResult;
use FredBradley\SmugMug\Exceptions\FailedException;
use FredBradley\SmugMug\Requests\Upload\CreateAlbumImageRequest;
use FredBradley\SmugMug\Requests\Upload\ReplaceImageMediaRequest;
use FredBradley\SmugMug\Requests\Upload\UploadImageRequest;
use FredBradley\SmugMug\Tests\Helpers\FakeSmugMug;
use FredBradley\SmugMug\Upload\Media;
use GuzzleHttp\Psr7\Utils;
use Saloon\Http\Faking\MockResponse;

function uploaded(): MockResponse
{
    return MockResponse::make(['stat' => 'ok', 'method' => 'smugmug.images.upload', 'Image' => [
        'ImageUri' => '/api/v2/image/Ab12Cd3-0',
        'AlbumImageUri' => '/api/v2/album/SJT3DX/image/Ab12Cd3-0',
        'StatusImageReplaceUri' => null,
        'URL' => 'https://cmac.smugmug.com/Travel/i-Ab12Cd3',
    ]]);
}

it('uploads a file to the uploader with its details in X-Smug headers', function () {
    $mockClient = FakeSmugMug::respondWith([UploadImageRequest::class => uploaded()]);

    $result = FakeSmugMug::user()->uploads()->upload('SJT3DX', Media::fromContents('JPEGDATA', 'beach.jpg'), [
        'Title' => 'Beach',
        'Caption' => "Line one\r\nline two",
        'Keywords' => ['summer', 'sea'],
        'Latitude' => 50.5,
        'Hidden' => false,
    ]);
    $sent = FakeSmugMug::sent($mockClient, UploadImageRequest::class)[0];

    expect($result)->toBeInstanceOf(UploadResult::class)
        ->and($result->ImageUri)->toBe('/api/v2/image/Ab12Cd3-0')
        ->and($result->imageKey())->toBe('Ab12Cd3-0')
        ->and($result->URL)->toBe('https://cmac.smugmug.com/Travel/i-Ab12Cd3')
        ->and($sent->getMethod())->toBe('POST')
        ->and((string) $sent->getUri())->toBe('https://upload.smugmug.com/')
        ->and((string) $sent->getBody())->toBe('JPEGDATA')
        ->and($sent->getHeaderLine('Content-MD5'))->toBe(base64_encode(md5('JPEGDATA', true)))
        ->and($sent->getHeaderLine('Content-Length'))->toBe('8')
        ->and($sent->getHeaderLine('Content-Type'))->toBe('image/jpeg')
        ->and($sent->getHeaderLine('X-Smug-AlbumUri'))->toBe('/api/v2/album/SJT3DX')
        ->and($sent->getHeaderLine('X-Smug-ResponseType'))->toBe('JSON')
        ->and($sent->getHeaderLine('X-Smug-Version'))->toBe('v2')
        ->and($sent->getHeaderLine('X-Smug-FileName'))->toBe('beach.jpg')
        ->and($sent->getHeaderLine('X-Smug-Title'))->toBe('Beach')
        ->and($sent->getHeaderLine('X-Smug-Caption'))->toBe('Line one line two')
        ->and($sent->getHeaderLine('X-Smug-Keywords'))->toBe('summer; sea')
        ->and($sent->getHeaderLine('X-Smug-Latitude'))->toBe('50.5')
        ->and($sent->getHeaderLine('X-Smug-Hidden'))->toBe('false')
        ->and($sent->hasHeader('X-Smug-ImageUri'))->toBeFalse()
        ->and(FakeSmugMug::oauthParameters($sent)['oauth_token'])->toBe('user-token')
        ->and(FakeSmugMug::signatureIsValid($sent))->toBeTrue();
});

it('replaces an image with the uploader', function () {
    $mockClient = FakeSmugMug::respondWith([UploadImageRequest::class => uploaded()]);
    $path = tempnam(sys_get_temp_dir(), 'smug').'.png';
    file_put_contents($path, 'PNGDATA');

    FakeSmugMug::user()->uploads()->replace('/api/v2/album/SJT3DX', '/api/v2/image/Ab12Cd3-0', $path);
    $sent = FakeSmugMug::sent($mockClient, UploadImageRequest::class)[0];
    unlink($path);

    expect($sent->getHeaderLine('X-Smug-ImageUri'))->toBe('/api/v2/image/Ab12Cd3-0')
        ->and($sent->getHeaderLine('X-Smug-AlbumUri'))->toBe('/api/v2/album/SJT3DX')
        ->and($sent->getHeaderLine('X-Smug-FileName'))->toBe(basename($path))
        ->and((string) $sent->getBody())->toBe('PNGDATA');
});

it('refuses an upload without a token, or with details the uploader does not take', function () {
    FakeSmugMug::respondWith([UploadImageRequest::class => uploaded()]);

    expect(fn () => FakeSmugMug::connector()->uploads()->upload('SJT3DX', Media::fromContents('x', 'a.jpg')))->toThrow(LogicException::class, 'needs an access token')
        ->and(fn () => new UploadImageRequest('SJT3DX', Media::fromContents('x', 'a.jpg'), ['Privacy' => 'Public']))->toThrow(InvalidArgumentException::class, 'not something the uploader takes')
        ->and(fn () => new UploadImageRequest('../../x', Media::fromContents('x', 'a.jpg')))->toThrow(InvalidArgumentException::class);
});

it('throws when the uploader says the upload failed', function () {
    FakeSmugMug::respondWith([UploadImageRequest::class => MockResponse::make(['stat' => 'fail', 'code' => 5, 'message' => 'Wrong MD5 sum'])]);

    expect(fn () => FakeSmugMug::user()->uploads()->upload('SJT3DX', Media::fromContents('x', 'a.jpg')))
        ->toThrow(FailedException::class, 'SmugMug responded 200 to POST https://upload.smugmug.com/: Wrong MD5 sum');
});

it('takes the upload timeout from config', function () {
    config()->set('smugmug.timeouts.upload', 900);
    expect((new UploadImageRequest('SJT3DX', Media::fromContents('x', 'a.jpg')))->getRequestTimeout())->toBe(900.0);

    config()->set('smugmug.timeouts.upload', 0);
    expect((new UploadImageRequest('SJT3DX', Media::fromContents('x', 'a.jpg')))->getRequestTimeout())->toBe(300.0);
});

it('adds an image to an album through the API as multipart form data', function () {
    $mockClient = FakeSmugMug::respondWith([CreateAlbumImageRequest::class => FakeSmugMug::object('AlbumImage', ['ImageKey' => 'Ab12Cd3-0'])]);

    $image = FakeSmugMug::user()->uploads()->toAlbum('SJT3DX', Media::fromContents('JPEGDATA', 'beach.jpg'), ['Title' => 'Beach', 'Keywords' => ['a', 'b'], 'Hidden' => true]);
    $sent = FakeSmugMug::sent($mockClient, CreateAlbumImageRequest::class)[0];
    $body = (string) $sent->getBody();

    expect($image)->toBeInstanceOf(AlbumImage::class)
        ->and($image->ImageKey)->toBe('Ab12Cd3-0')
        ->and((string) $sent->getUri())->toBe('https://api.smugmug.com/api/v2/album/SJT3DX!images')
        ->and($sent->getHeaderLine('Content-Type'))->toStartWith('multipart/form-data')
        ->and($body)->toContain('name="ByteCount"')->toContain('8')
        ->and($body)->toContain('name="MD5Sum"')->toContain(md5('JPEGDATA'))
        ->and($body)->toContain('name="Keywords"')->toContain('a; b')
        ->and($body)->toContain('name="Hidden"')->toContain('true')
        ->and($body)->toContain('name="Media"; filename="beach.jpg"')->toContain('JPEGDATA')
        ->and(FakeSmugMug::signatureIsValid($sent))->toBeTrue()
        ->and(fn () => new CreateAlbumImageRequest('SJT3DX', Media::fromContents('x', 'a.jpg'), ['Privacy' => 'x']))->toThrow(InvalidArgumentException::class);
});

it('replaces an image\'s file through the API', function () {
    $mockClient = FakeSmugMug::respondWith([ReplaceImageMediaRequest::class => FakeSmugMug::action(['Done' => true])]);

    $result = FakeSmugMug::user()->uploads()->replaceMedia('Ab12Cd3-0', Media::fromContents('NEW', 'new.jpg'));
    $sent = FakeSmugMug::sent($mockClient, ReplaceImageMediaRequest::class)[0];

    expect($result)->toBe(['Uri' => '/api/v2/example', 'Done' => true])
        ->and((string) $sent->getUri())->toBe('https://api.smugmug.com/api/v2/image/Ab12Cd3-0!replace')
        ->and((string) $sent->getBody())->toContain('name="Media"; filename="new.jpg"')->toContain(md5('NEW'));
});

it('describes a file to upload', function () {
    $path = tempnam(sys_get_temp_dir(), 'smug');
    file_put_contents($path, 'DATA');
    $media = Media::fromPath($path, 'renamed.mov');

    expect($media->fileName)->toBe('renamed.mov')
        ->and($media->md5)->toBe(md5('DATA'))
        ->and($media->size)->toBe(4)
        ->and(stream_get_contents($media->contents()))->toBe('DATA')
        ->and(stream_get_contents($media->contents()))->toBe('DATA')
        ->and(Media::fromContents('x', 'clip.MP4')->mimeType)->toBe('video/mp4')
        ->and(Media::fromContents('x', 'file.xyz')->mimeType)->toBe('application/octet-stream')
        ->and(Media::fromContents('x', 'a.heic')->mimeType)->toBe('image/heic')
        ->and(new Media(Utils::streamFor('abc'), 'a.jpg', md5('abc'), 3))->toBeInstanceOf(Media::class)
        ->and(fn () => Media::fromPath('/no/such/file'))->toThrow(InvalidArgumentException::class)
        ->and(fn () => new Media('not a stream', 'a', md5('a'), 1))->toThrow(InvalidArgumentException::class)
        ->and(fn () => new Media(fopen('php://memory', 'r'), 'a', 'nope', 1))->toThrow(InvalidArgumentException::class);

    unlink($path);
});
