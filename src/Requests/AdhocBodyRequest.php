<?php

namespace FredBradley\SmugMug\Requests;

use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Support\Path;
use JsonSerializable;
use Saloon\Enums\Method;

/**
 * A POST, PATCH or PUT of JSON to any URI SmugMug serves, as SmugMug::post() and SmugMug::patch() send one.
 */
class AdhocBodyRequest extends WriteRequest
{
    protected string $responseShape = self::AUTO;

    protected string $uri;

    /**
     * @param  array<mixed>|JsonSerializable  $body
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(Method $method, string $uri, array|JsonSerializable $body = [], Options|array $options = [])
    {
        $this->method = $method;
        $this->uri = Path::uri($uri);
        $this->setBody($body, $options);
    }

    public function resolveEndpoint(): string
    {
        return $this->uri;
    }
}
