<?php

namespace FredBradley\SmugMug\Tests\Fixtures;

use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ListRequest;

class ExampleListRequest extends ListRequest
{
    protected ?string $dto = ExampleData::class;

    /**
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(Options|array $options = [])
    {
        $this->setOptions($options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/example!items';
    }
}
