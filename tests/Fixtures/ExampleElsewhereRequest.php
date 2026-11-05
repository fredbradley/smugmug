<?php

namespace FredBradley\SmugMug\Tests\Fixtures;

use FredBradley\SmugMug\Requests\SmugMugRequest;
use Saloon\Enums\Method;

class ExampleElsewhereRequest extends SmugMugRequest
{
    public ?bool $allowBaseUrlOverride = true;

    protected Method $method = Method::GET;

    public function __construct(private readonly string $url = 'https://evil.example.test/steal') {}

    public function resolveEndpoint(): string
    {
        return $this->url;
    }
}
