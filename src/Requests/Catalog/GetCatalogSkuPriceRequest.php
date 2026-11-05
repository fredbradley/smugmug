<?php

namespace FredBradley\SmugMug\Requests\Catalog;

use FredBradley\SmugMug\Data\Commerce\CatalogSkuPrice;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ItemRequest;
use FredBradley\SmugMug\Support\Path;

/**
 * What a SKU costs: GET /api/v2/catalog/sku/{skuId}!price.
 *
 * Parameters (in the options):
 *  - Album, Image: the album or image it is sold from
 *  - Currency
 *  - ApplyColorCorrection
 *  - PriceType: Base, Pro or Wholesale
 */
class GetCatalogSkuPriceRequest extends ItemRequest
{
    protected ?string $dto = CatalogSkuPrice::class;

    /**
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $skuId, Options|array $options = [])
    {
        $this->setOptions($options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/catalog/sku/'.Path::segment($this->skuId).'!price';
    }
}
