<?php

namespace FredBradley\SmugMug\Requests\Catalog;

use FredBradley\SmugMug\Data\Commerce\CatalogCategory;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ListRequest;
use FredBradley\SmugMug\Support\Path;

/**
 * A vendor's product categories: GET /api/v2/catalog/vendor/{vendorId}!categories.
 */
class GetCatalogCategoriesRequest extends ListRequest
{
    protected ?string $dto = CatalogCategory::class;

    /**
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $vendorId, Options|array $options = [])
    {
        $this->setOptions($options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/catalog/vendor/'.Path::segment($this->vendorId).'!categories';
    }
}
