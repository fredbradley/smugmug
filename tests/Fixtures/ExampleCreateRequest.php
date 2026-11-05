<?php

namespace FredBradley\SmugMug\Tests\Fixtures;

use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\WriteRequest;
use JsonSerializable;

class ExampleCreateRequest extends WriteRequest
{
    protected ?string $dto = ExampleData::class;

    /**
     * @param  array<mixed>|JsonSerializable  $body
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(array|JsonSerializable $body = [], Options|array $options = [])
    {
        $this->setBody($body, $options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/example';
    }
}
