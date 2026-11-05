<?php

namespace FredBradley\SmugMug\Requests\Catalog;

use FredBradley\SmugMug\Data\Commerce\CatalogSkuPrice;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ListRequest;
use FredBradley\SmugMug\Support\Path;

/**
 * A print lab's prices: GET /api/v2/catalog/printvendor/{vendorId}!prices.
 *
 * Parameters (in the options):
 *  - Currency
 *  - ApplyColorCorrection
 *  - PriceType: Base, Pro or Wholesale
 */
class GetCatalogPrintVendorPricesRequest extends ListRequest
{
    protected ?string $dto = CatalogSkuPrice::class;

    /**
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $vendorId, Options|array $options = [])
    {
        $this->setOptions($options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/catalog/printvendor/'.Path::segment($this->vendorId).'!prices';
    }
}
