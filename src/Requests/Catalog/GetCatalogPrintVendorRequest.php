<?php

namespace FredBradley\SmugMug\Requests\Catalog;

use FredBradley\SmugMug\Data\Commerce\CatalogVendor;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ItemRequest;
use FredBradley\SmugMug\Support\Path;

/**
 * A print lab: GET /api/v2/catalog/printvendor/{vendorId}.
 *
 * Parameters (in the options):
 *  - ApplyColorCorrection
 *  - PriceType: Base, Pro or Wholesale
 */
class GetCatalogPrintVendorRequest extends ItemRequest
{
    protected ?string $dto = CatalogVendor::class;

    /**
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $vendorId, Options|array $options = [])
    {
        $this->setOptions($options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/catalog/printvendor/'.Path::segment($this->vendorId);
    }
}
