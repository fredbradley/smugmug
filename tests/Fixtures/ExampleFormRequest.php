<?php

namespace FredBradley\SmugMug\Tests\Fixtures;

use FredBradley\SmugMug\Requests\SmugMugRequest;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Traits\Body\HasFormBody;

class ExampleFormRequest extends SmugMugRequest implements HasBody
{
    use HasFormBody;

    protected Method $method = Method::POST;

    protected string $responseShape = self::RESPONSE;

    /**
     * @param  array<string, mixed>  $fields
     */
    public function __construct(private readonly array $fields = []) {}

    public function resolveEndpoint(): string
    {
        return '/api/v2/example!form';
    }

    /**
     * @return array<string, mixed>
     */
    protected function defaultBody(): array
    {
        return $this->fields;
    }
}
