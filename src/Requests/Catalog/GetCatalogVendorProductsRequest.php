<?php

namespace FredBradley\SmugMug\Requests\Catalog;

use FredBradley\SmugMug\Data\Commerce\CatalogProduct;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ListRequest;
use FredBradley\SmugMug\Support\Path;

/**
 * A vendor's products: GET /api/v2/catalog/vendor/{vendorId}!products.
 *
 * Parameters (in the options):
 *  - ShowDisabled: include disabled products
 */
class GetCatalogVendorProductsRequest extends ListRequest
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
        return '/api/v2/catalog/vendor/'.Path::segment($this->vendorId).'!products';
    }
}
