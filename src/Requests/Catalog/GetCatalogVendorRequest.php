<?php

namespace FredBradley\SmugMug\Requests\Catalog;

use FredBradley\SmugMug\Data\Commerce\CatalogVendor;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ItemRequest;
use FredBradley\SmugMug\Support\Path;

/**
 * A vendor of the catalogue, such as SmugMug: GET /api/v2/catalog/vendor/{vendorId}.
 */
class GetCatalogVendorRequest extends ItemRequest
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
        return '/api/v2/catalog/vendor/'.Path::segment($this->vendorId);
    }
}
