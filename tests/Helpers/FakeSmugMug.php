<?php

namespace FredBradley\SmugMug\Tests\Helpers;

use FredBradley\SmugMug\Auth\OAuth1Authenticator;
use FredBradley\SmugMug\SmugMugConnector;
use Psr\Http\Message\RequestInterface;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;
use Saloon\Http\PendingRequest;
use Saloon\Http\Request;
use Saloon\Http\Response;

class FakeSmugMug
{
    public const API_URL = 'https://api.smugmug.com';

    public const API_KEY = 'test-api-key';

    public const API_SECRET = 'test-api-secret';

    public const TOKEN = 'user-token';

    public const TOKEN_SECRET = 'user-token-secret';

    /**
     * The Locator SmugMug answers a request with: its model's first, or Album for a request that maps by Locator.
     *
     * @param  class-string|null  $dto
     */
    public static function locatorFor(?string $dto): string
    {
        if ($dto === null) {
            return 'Album';
        }

        $models = require __DIR__.'/../Spec/models.php';

        return $models[class_basename($dto)][1][0];
    }

    /**
     * Fake SmugMug: each request class answers with the given response.
     *
     * @param  array<class-string<Request>|string, MockResponse|callable(PendingRequest): MockResponse>  $responses
     */
    public static function respondWith(array $responses = []): MockClient
    {
        MockClient::destroyGlobal();

        return MockClient::global($responses);
    }

    /**
     * A connector for the application alone (the API key), or for a user when given a token.
     */
    public static function connector(?string $token = null, ?string $tokenSecret = null, string $apiKey = self::API_KEY, ?string $apiSecret = self::API_SECRET): SmugMugConnector
    {
        return new SmugMugConnector($apiKey, $apiSecret, $token, $token !== null ? ($tokenSecret ?? self::TOKEN_SECRET) : null);
    }

    /**
     * A connector signing for a user.
     */
    public static function user(): SmugMugConnector
    {
        return self::connector(self::TOKEN, self::TOKEN_SECRET);
    }

    /**
     * An answer as SmugMug sends it: {"Response": {"Uri", "Locator", "LocatorType", "<Locator>": …, "Pages"}, "Code",
     * "Message"}.
     *
     * @param  array<mixed>|null  $payload
     * @param  array<string, mixed>|null  $pages
     * @param  array<string, mixed>  $expansions
     * @param  array<string, string>  $headers
     */
    public static function envelope(?string $locator = null, ?array $payload = null, string $locatorType = 'Object', ?array $pages = null, int $status = 200, array $expansions = [], array $headers = []): MockResponse
    {
        $response = ['Uri' => '/api/v2/example', 'UriDescription' => 'Example'];

        if ($locator !== null) {
            $response += ['Locator' => $locator, 'LocatorType' => $locatorType, $locator => $payload];
        }

        if ($pages !== null) {
            $response['Pages'] = $pages;
        }

        $body = ['Response' => $response, 'Code' => $status, 'Message' => $status < 300 ? 'Ok' : 'Error'];

        if ($expansions !== []) {
            $body['Expansions'] = $expansions;
        }

        return MockResponse::make($body, $status, $headers);
    }

    /**
     * One object.
     *
     * @param  array<string, mixed>  $object
     */
    public static function object(string $locator, array $object): MockResponse
    {
        return self::envelope($locator, $object);
    }

    /**
     * A page of objects, with Pages as SmugMug describes it: NextPage while there are more.
     *
     * @param  list<array<string, mixed>>  $items
     */
    public static function objects(string $locator, array $items, ?int $total = null, int $start = 1, ?int $requested = null): MockResponse
    {
        $total ??= count($items);
        $count = count($items);
        $requested ??= max($count, 1);
        $pages = ['Total' => $total, 'Start' => $start, 'Count' => $count, 'RequestedCount' => $requested, 'FirstPage' => '/api/v2/example?start=1&count='.$requested];

        if ($start + $count <= $total) {
            $pages['NextPage'] = '/api/v2/example?start='.($start + $count).'&count='.$requested;
        }

        return self::envelope($locator, $items, 'Objects', $pages);
    }

    /**
     * SmugMug's answer to an action: the envelope's Response without an object.
     *
     * @param  array<string, mixed>  $response
     */
    public static function action(array $response = []): MockResponse
    {
        return MockResponse::make(['Response' => ['Uri' => '/api/v2/example', ...$response], 'Code' => 200, 'Message' => 'Ok']);
    }

    /**
     * SmugMug's answer when a request failed.
     */
    public static function failure(int $status, string $message = 'Something went wrong'): MockResponse
    {
        return MockResponse::make(['Code' => $status, 'Message' => $message], $status);
    }

    /**
     * Send one request through a connector to a faked SmugMug that answers it with the given response.
     */
    public static function send(Request $request, MockResponse $response, ?SmugMugConnector $connector = null): Response
    {
        self::respondWith([$request::class => $response]);

        return ($connector ?? self::connector())->send($request);
    }

    /**
     * The request SmugMug received for the given request class, as Saloon built it.
     *
     * @param  class-string<Request>  $requestClass
     */
    public static function received(MockClient $mockClient, string $requestClass): PendingRequest
    {
        return $mockClient->findResponseByRequest($requestClass)->getPendingRequest();
    }

    /**
     * The HTTP requests SmugMug received for the given request class, as they went out (signed).
     *
     * @param  class-string<Request>  $requestClass
     * @return list<RequestInterface>
     */
    public static function sent(MockClient $mockClient, string $requestClass): array
    {
        return collect($mockClient->getRecordedResponses())
            ->filter(fn (Response $response) => $response->getRequest() instanceof $requestClass)
            ->map(fn (Response $response) => $response->getPsrRequest())
            ->values()
            ->all();
    }

    /**
     * The URIs SmugMug received for the given request class, as sent.
     *
     * @param  class-string<Request>  $requestClass
     * @return list<string>
     */
    public static function urisReceived(MockClient $mockClient, string $requestClass): array
    {
        return array_map(fn (RequestInterface $request) => (string) $request->getUri(), self::sent($mockClient, $requestClass));
    }

    /**
     * The OAuth parameters of a signed request's Authorization header.
     *
     * @return array<string, string>
     */
    public static function oauthParameters(RequestInterface $request): array
    {
        $header = $request->getHeaderLine('Authorization');
        preg_match_all('/(\w+)="([^"]*)"/', $header, $matches, PREG_SET_ORDER);

        return collect($matches)->mapWithKeys(fn (array $match) => [rawurldecode($match[1]) => rawurldecode($match[2])])->all();
    }

    /**
     * Whether a request's signature is what the application's and token's secrets make of it.
     */
    public static function signatureIsValid(RequestInterface $request, string $consumerSecret = self::API_SECRET, ?string $tokenSecret = self::TOKEN_SECRET): bool
    {
        $oauth = self::oauthParameters($request);
        $signature = $oauth['oauth_signature'] ?? '';
        unset($oauth['oauth_signature']);

        $parameters = [];

        foreach (array_filter(explode('&', $request->getUri()->getQuery())) as $pair) {
            [$name, $value] = array_pad(explode('=', $pair, 2), 2, '');
            $parameters[] = [urldecode($name), urldecode($value)];
        }

        if (str_starts_with($request->getHeaderLine('Content-Type'), 'application/x-www-form-urlencoded')) {
            foreach (array_filter(explode('&', (string) $request->getBody())) as $pair) {
                [$name, $value] = array_pad(explode('=', $pair, 2), 2, '');
                $parameters[] = [urldecode($name), urldecode($value)];
            }
        }

        foreach ($oauth as $name => $value) {
            $parameters[] = [$name, $value];
        }

        $signer = new OAuth1Authenticator('unused', $consumerSecret, null, $tokenSecret);

        return hash_equals($signer->signature($request->getMethod(), (string) $request->getUri(), $parameters), $signature);
    }
}
