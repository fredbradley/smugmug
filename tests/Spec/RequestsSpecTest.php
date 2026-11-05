<?php

use FredBradley\SmugMug\Data\Album\Album;
use FredBradley\SmugMug\Requests\SmugMugRequest;
use FredBradley\SmugMug\Tests\Helpers\FakeSmugMug;
use Illuminate\Support\Collection;
use Saloon\Contracts\Body\HasBody;
use Saloon\PaginationPlugin\Contracts\Paginatable;

$endpoints = require __DIR__.'/endpoints.php';

it('sends the method and path SmugMug documents, and maps the response', function (string $class, array $args, string $method, string $path, string $shape, ?string $dto, bool $hasBody, bool $paginatable) {
    /** @var SmugMugRequest $request */
    $request = new $class(...$args);

    expect($request)->toBeInstanceOf(SmugMugRequest::class)
        ->and($request->getMethod()->value)->toBe($method)
        ->and($request->resolveEndpoint())->toBe($path)
        ->and($request->dtoClass())->toBe($dto)
        ->and($request->responseShape())->toBe($shape)
        ->and($request instanceof Paginatable)->toBe($paginatable)
        ->and($request instanceof HasBody)->toBe($hasBody)
        ->and($request->isSafe())->toBe($method === 'GET');

    $sample = ['Uri' => '/api/v2/example/1', 'Name' => 'x'];
    $locator = FakeSmugMug::locatorFor($dto);
    $mock = match ($shape) {
        'list' => FakeSmugMug::objects($locator, [$sample]),
        'one' => FakeSmugMug::object($locator, $sample),
        'response' => FakeSmugMug::action(['Done' => true]),
        default => FakeSmugMug::envelope(),
    };

    $response = FakeSmugMug::send($request, $mock, FakeSmugMug::user());
    $result = $response->dto();

    expect((string) $response->getPsrRequest()->getUri())->toStartWith(FakeSmugMug::API_URL.$path)
        ->and(FakeSmugMug::signatureIsValid($response->getPsrRequest()))->toBeTrue();

    match ($shape) {
        'list' => expect($result)->toBeInstanceOf(Collection::class)->toHaveCount(1)->and($result->first())->toBeInstanceOf($dto),
        'one' => expect($result)->toBeInstanceOf($dto ?? Album::class)->and($result->Uri ?? $result->extra['Uri'] ?? null)->toBe('/api/v2/example/1'),
        'response' => expect($result)->toBe(['Uri' => '/api/v2/example', 'Done' => true]),
        default => expect($result)->toBeNull(),
    };

    if ($hasBody) {
        expect(json_decode((string) $response->getPsrRequest()->getBody(), true))->toBe(['Name' => 'x'])
            ->and($response->getPsrRequest()->getHeaderLine('Content-Type'))->toBe('application/json');
    }
})->with($endpoints);

it('lists every generated request class once', function () use ($endpoints) {
    $classes = collect(glob(__DIR__.'/../../src/Requests/*/*Request.php'))
        ->reject(fn (string $file) => in_array(basename(dirname($file)), ['OAuth', 'Upload'], true))
        ->map(fn (string $file) => 'FredBradley\\SmugMug\\Requests\\'.basename(dirname($file)).'\\'.basename($file, '.php'))
        ->sort()
        ->values();
    $listed = collect($endpoints)->map(fn (array $row) => $row[0])->sort()->values();

    expect($listed->all())->toBe($classes->all())
        ->and($listed->unique())->toHaveCount($listed->count())
        ->and($classes)->toHaveCount(322);
});

it('documents every request in ENDPOINTS.md', function () use ($endpoints) {
    $markdown = file_get_contents(__DIR__.'/../../ENDPOINTS.md');
    $handWritten = ['OAuth\\GetRequestTokenRequest', 'OAuth\\GetAccessTokenRequest', 'Upload\\UploadImageRequest', 'Upload\\CreateAlbumImageRequest', 'Upload\\ReplaceImageMediaRequest'];

    foreach ([...collect($endpoints)->map(fn (array $row) => str_replace('FredBradley\\SmugMug\\Requests\\', '', $row[0]))->all(), ...$handWritten] as $class) {
        expect($markdown)->toContain('`'.$class.'`');
    }
});

it('calls every documented method and path once', function () use ($endpoints) {
    $operations = collect($endpoints)->map(fn (array $row) => $row[2].' '.(new ReflectionClass($row[0]))->getShortName());
    $paths = collect($endpoints)->map(fn (array $row) => $row[2].' '.$row[3]);

    expect($operations->unique())->toHaveCount($operations->count())
        ->and($paths->unique())->toHaveCount($paths->count());
});
