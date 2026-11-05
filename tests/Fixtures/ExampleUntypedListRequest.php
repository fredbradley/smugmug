<?php

namespace FredBradley\SmugMug\Tests\Fixtures;

use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ListRequest;

class ExampleUntypedListRequest extends ListRequest
{
    /**
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(Options|array $options = [])
    {
        $this->setOptions($options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/example!things';
    }
}
