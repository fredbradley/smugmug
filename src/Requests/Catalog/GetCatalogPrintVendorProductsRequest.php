<?php

namespace FredBradley\SmugMug\Requests\Catalog;

use FredBradley\SmugMug\Data\Commerce\CatalogProduct;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ListRequest;
use FredBradley\SmugMug\Support\Path;

/**
 * A print lab's products: GET /api/v2/catalog/printvendor/{vendorId}!products.
 *
 * Parameters (in the options):
 *  - ShowDisabled: include disabled products
 */
class GetCatalogPrintVendorProductsRequest extends ListRequest
{
    protected ?string $dto = CatalogProduct::class;

    /**
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $vendorId, Options|array $options = [])
    {
        $this->setOptions($options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/catalog/printvendor/'.Path::segment($this->vendorId).'!products';
    }
}
