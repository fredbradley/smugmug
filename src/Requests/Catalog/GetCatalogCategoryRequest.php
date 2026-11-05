<?php

namespace FredBradley\SmugMug\Requests\Catalog;

use FredBradley\SmugMug\Data\Commerce\CatalogCategory;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ItemRequest;
use FredBradley\SmugMug\Support\Path;

/**
 * A product category: GET /api/v2/catalog/vendor/{vendorId}/category/{categoryId}.
 */
class GetCatalogCategoryRequest extends ItemRequest
{
    protected ?string $dto = CatalogCategory::class;

    /**
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $vendorId, public readonly string $categoryId, Options|array $options = [])
    {
        $this->setOptions($options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/catalog/vendor/'.Path::segment($this->vendorId).'/category/'.Path::segment($this->categoryId);
    }
}
