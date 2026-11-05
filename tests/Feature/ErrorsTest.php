<?php

use FredBradley\SmugMug\Exceptions\ConflictException;
use FredBradley\SmugMug\Exceptions\FailedException;
use FredBradley\SmugMug\Exceptions\ForbiddenException;
use FredBradley\SmugMug\Exceptions\InvalidRequestException;
use FredBradley\SmugMug\Exceptions\NotFoundException;
use FredBradley\SmugMug\Exceptions\PaymentRequiredException;
use FredBradley\SmugMug\Exceptions\RateLimitedException;
use FredBradley\SmugMug\Exceptions\ServerErrorException;
use FredBradley\SmugMug\Exceptions\UnauthorizedException;
use FredBradley\SmugMug\SmugMugApiException;
use FredBradley\SmugMug\Tests\Fixtures\ExampleCreateRequest;
use FredBradley\SmugMug\Tests\Fixtures\ExampleItemRequest;
use FredBradley\SmugMug\Tests\Helpers\FakeSmugMug;
use Saloon\Http\Faking\MockResponse;

it('throws the exception for the status', function (int $status, string $class) {
    $response = FakeSmugMug::send(new ExampleItemRequest('K1'), FakeSmugMug::failure($status, 'Something happened'));

    expect(fn () => $response->dto())->toThrow($class, "SmugMug responded $status to GET /api/v2/example/K1: Something happened")
        ->and(fn () => $response->throw())->toThrow($class);
})->with([
    [400, InvalidRequestException::class],
    [401, UnauthorizedException::class],
    [402, PaymentRequiredException::class],
    [403, ForbiddenException::class],
    [404, NotFoundException::class],
    [405, InvalidRequestException::class],
    [406, InvalidRequestException::class],
    [409, ConflictException::class],
    [429, RateLimitedException::class],
    [408, ServerErrorException::class],
    [500, ServerErrorException::class],
    [503, ServerErrorException::class],
    [418, SmugMugApiException::class],
]);

it('throws when a successful status carries a failed envelope', function () {
    $response = FakeSmugMug::send(new ExampleCreateRequest(['Name' => '']), MockResponse::make(['Code' => 400, 'Message' => 'Name is required']));

    try {
        $response->dto();
        $this->fail('No exception');
    } catch (FailedException $exception) {
        expect($exception)->toBeInstanceOf(SmugMugApiException::class)
            ->and($exception->errorMessage())->toBe('Name is required')
            ->and($exception->getResponse()->status())->toBe(200)
            ->and($exception->getMessage())->toBe('SmugMug responded 200 to POST /api/v2/example: Name is required');
    }
});

it('tells a caller when to try again after the rate limit', function () {
    $response = FakeSmugMug::send(new ExampleItemRequest, MockResponse::make(['Code' => 429, 'Message' => 'Too Many Requests'], 429, ['Retry-After' => '12']));

    try {
        $response->dto();
    } catch (RateLimitedException $exception) {
        expect($exception->retryAfter())->toBe(12)
            ->and($exception->rateLimit()->retryAfter)->toBe(12);
    }

    $reset = time() + 20;
    $response = FakeSmugMug::send(new ExampleItemRequest, MockResponse::make(['Code' => 429], 429, ['X-RateLimit-Reset' => (string) $reset, 'X-RateLimit-Remaining' => '0']));

    try {
        $response->dto();
    } catch (RateLimitedException $exception) {
        expect($exception->retryAfter())->toBeGreaterThanOrEqual(19)->toBeLessThanOrEqual(20)
            ->and($exception->rateLimit()->exhausted())->toBeTrue();
    }

    $response = FakeSmugMug::send(new ExampleItemRequest, FakeSmugMug::failure(429));
    expect(fn () => $response->dto())->toThrow(RateLimitedException::class);

    try {
        $response->dto();
    } catch (RateLimitedException $exception) {
        expect($exception->retryAfter())->toBeNull();
    }
});

it('describes an answer that is not the envelope', function () {
    $html = FakeSmugMug::send(new ExampleItemRequest, MockResponse::make('<html><head><title>502 Bad Gateway</title></head></html>', 502, ['Content-Type' => 'text/html']));
    expect(fn () => $html->dto())->toThrow(ServerErrorException::class, "SmugMug responded 502 to GET /api/v2/example/K1 with an HTML error page ('502 Bad Gateway')");

    $untitled = FakeSmugMug::send(new ExampleItemRequest, MockResponse::make('<html>down</html>', 503));
    expect(fn () => $untitled->dto())->toThrow(ServerErrorException::class, 'SmugMug responded 503 to GET /api/v2/example/K1 with an HTML error page');

    $text = FakeSmugMug::send(new ExampleItemRequest, MockResponse::make('Service down', 503));
    expect(fn () => $text->dto())->toThrow(ServerErrorException::class, 'SmugMug responded 503 to GET /api/v2/example/K1: Service down');

    $empty = FakeSmugMug::send(new ExampleItemRequest, MockResponse::make('', 500));
    expect(fn () => $empty->dto())->toThrow(ServerErrorException::class, 'SmugMug responded 500 to GET /api/v2/example/K1');

    $list = FakeSmugMug::send(new ExampleItemRequest, MockResponse::make([['x']], 404));
    expect(fn () => $list->dto())->toThrow(NotFoundException::class, 'SmugMug responded 404 to GET /api/v2/example/K1');

    $page = FakeSmugMug::send(new ExampleItemRequest, MockResponse::make('<html><title>Maintenance</title></html>', 200, ['Content-Type' => 'text/html']));
    expect(fn () => $page->dto())->toThrow(ServerErrorException::class);

    $long = FakeSmugMug::send(new ExampleItemRequest, MockResponse::make(str_repeat('x', 300), 500));
    expect(fn () => $long->dto())->toThrow(ServerErrorException::class, str_repeat('x', 197).'...');
});

it('says nothing of the query string, which may carry the API key', function () {
    $response = FakeSmugMug::send(new ExampleItemRequest('K1', ['Text' => 'secret']), FakeSmugMug::failure(404, 'Not Found'));

    expect(fn () => $response->dto())->toThrow(NotFoundException::class, 'SmugMug responded 404 to GET /api/v2/example/K1: Not Found');

    try {
        $response->dto();
    } catch (NotFoundException $exception) {
        expect($exception->getMessage())->not->toContain('APIKey')->not->toContain('test-api-key');
    }
});

it('reads a message in other shapes, or none', function () {
    $error = FakeSmugMug::send(new ExampleItemRequest, MockResponse::make(['error' => 'invalid_token'], 401));
    expect(fn () => $error->dto())->toThrow(UnauthorizedException::class, 'SmugMug responded 401 to GET /api/v2/example/K1: invalid_token');

    $none = FakeSmugMug::send(new ExampleItemRequest, MockResponse::make(['Code' => 500], 500));

    try {
        $none->dto();
    } catch (ServerErrorException $exception) {
        expect($exception->errorMessage())->toBeNull()
            ->and($exception->getMessage())->toBe('SmugMug responded 500 to GET /api/v2/example/K1');
    }
});
