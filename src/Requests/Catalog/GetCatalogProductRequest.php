<?php

namespace FredBradley\SmugMug\Requests\Catalog;

use FredBradley\SmugMug\Data\Commerce\CatalogProduct;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ItemRequest;
use FredBradley\SmugMug\Support\Path;

/**
 * A product, by id: GET /api/v2/catalog/product/{productId}.
 */
class GetCatalogProductRequest extends ItemRequest
{
    protected ?string $dto = CatalogProduct::class;

    /**
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $productId, Options|array $options = [])
    {
        $this->setOptions($options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/catalog/product/'.Path::segment($this->productId);
    }
}
