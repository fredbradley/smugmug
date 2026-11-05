<?php

namespace FredBradley\SmugMug\Requests\Pricelist;

use FredBradley\SmugMug\Data\Commerce\PricelistSkuPrice;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ItemRequest;
use FredBradley\SmugMug\Support\Path;

/**
 * The price of one SKU on a pricelist: GET /api/v2/pricelist/{pricelistKey}/skuprice/{skuId}.
 *
 * Parameters (in the options):
 *  - Currency
 */
class GetPricelistSkuPriceRequest extends ItemRequest
{
    protected ?string $dto = PricelistSkuPrice::class;

    /**
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $pricelistKey, public readonly string $skuId, Options|array $options = [])
    {
        $this->setOptions($options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/pricelist/'.Path::segment($this->pricelistKey).'/skuprice/'.Path::segment($this->skuId);
    }
}
