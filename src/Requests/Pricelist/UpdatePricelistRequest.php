<?php

namespace FredBradley\SmugMug\Requests\Pricelist;

use FredBradley\SmugMug\Data\Commerce\Pricelist;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\WriteRequest;
use FredBradley\SmugMug\Support\Path;
use JsonSerializable;
use Saloon\Enums\Method;

/**
 * Change a pricelist: PATCH /api/v2/pricelist/{pricelistKey}.
 *
 * Parameters (in the body):
 *  - Name, ColorCorrection, Default, Currency
 */
class UpdatePricelistRequest extends WriteRequest
{
    protected Method $method = Method::PATCH;

    protected ?string $dto = Pricelist::class;

    /**
     * @param  array<mixed>|JsonSerializable  $body  SmugMug's field names and values, or a model.
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $pricelistKey, array|JsonSerializable $body = [], Options|array $options = [])
    {
        $this->setBody($body, $options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/pricelist/'.Path::segment($this->pricelistKey);
    }
}
