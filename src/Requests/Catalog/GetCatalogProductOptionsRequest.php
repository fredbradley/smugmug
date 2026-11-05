<?php

namespace FredBradley\SmugMug\Requests\Catalog;

use FredBradley\SmugMug\Data\Commerce\CatalogSku;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ListRequest;
use FredBradley\SmugMug\Support\Path;

/**
 * The options (SKUs) of a product: GET /api/v2/catalog/product/{productId}!options.
 */
class GetCatalogProductOptionsRequest extends ListRequest
{
    protected ?string $dto = CatalogSku::class;

    /**
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $productId, Options|array $options = [])
    {
        $this->setOptions($options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/catalog/product/'.Path::segment($this->productId).'!options';
    }
}
