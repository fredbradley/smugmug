<?php

namespace FredBradley\SmugMug\Tests\Fixtures;

use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ItemRequest;
use FredBradley\SmugMug\Support\Path;

class ExampleItemRequest extends ItemRequest
{
    protected ?string $dto = ExampleData::class;

    /**
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $key = 'K1', Options|array $options = [])
    {
        $this->setOptions($options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/example/'.Path::segment($this->key);
    }
}
