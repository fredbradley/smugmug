<?php

use FredBradley\SmugMug\Support\Path;

it('encodes a key for a path', function () {
    expect(Path::segment('SJT3DX'))->toBe('SJT3DX')
        ->and(Path::segment('jPPKD2c-1'))->toBe('jPPKD2c-1')
        ->and(Path::segment(12345))->toBe('12345')
        ->and(Path::segment('sam@example.com'))->toBe('sam%40example.com')
        ->and(Path::segment(['cmac', 'onethumb']))->toBe('cmac,onethumb');
});

it('refuses a key that would change the endpoint', function (string $key) {
    expect(fn () => Path::segment($key))->toThrow(InvalidArgumentException::class);
})->with(['', ' ', '.', '..', 'a/b', 'a!images', 'a?b', 'a#b', 'a,b']);

it('refuses an empty multi-get', function () {
    expect(fn () => Path::segment([]))->toThrow(InvalidArgumentException::class);
});

it('writes a folder path, each part encoded', function () {
    expect(Path::folder(''))->toBe('')
        ->and(Path::folder('/'))->toBe('')
        ->and(Path::folder('Travel/2024 Trips/'))->toBe('/Travel/2024%20Trips')
        ->and(fn () => Path::folder('Travel/../Private'))->toThrow(InvalidArgumentException::class);
});

it('takes a URI SmugMug gave, or one relative to the API', function (string $uri, string $expected) {
    expect(Path::uri($uri))->toBe($expected);
})->with([
    ['/api/v2/album/SJT3DX!images', '/api/v2/album/SJT3DX!images'],
    ['https://api.smugmug.com/api/v2/album/SJT3DX?count=2', '/api/v2/album/SJT3DX?count=2'],
    ['album/SJT3DX', '/api/v2/album/SJT3DX'],
    ['/album/SJT3DX', '/api/v2/album/SJT3DX'],
    ['/api/v2!authuser', '/api/v2!authuser'],
    ['/api/v2', '/api/v2'],
    ['album/X?', '/api/v2/album/X'],
]);

it('refuses a URI on another host or one that climbs out', function (string $uri) {
    expect(fn () => Path::uri($uri))->toThrow(InvalidArgumentException::class);
})->with(['https://evil.example.test/api/v2/album/x', 'http://api.smugmug.com/api/v2/album/x', '/api/v2/../services/x', 'ftp://api.smugmug.com/x']);
