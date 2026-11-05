<?php

namespace FredBradley\SmugMug\Requests\Pricelist;

use FredBradley\SmugMug\Data\Commerce\PricelistSkuPrice;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ListRequest;
use FredBradley\SmugMug\Support\Path;

/**
 * The prices on a pricelist: GET /api/v2/pricelist/{pricelistKey}!prices.
 *
 * Parameters (in the options):
 *  - Currency
 */
class GetPricelistPricesRequest extends ListRequest
{
    protected ?string $dto = PricelistSkuPrice::class;

    /**
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $pricelistKey, Options|array $options = [])
    {
        $this->setOptions($options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/pricelist/'.Path::segment($this->pricelistKey).'!prices';
    }
}
