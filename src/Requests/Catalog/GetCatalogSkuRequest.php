<?php

namespace FredBradley\SmugMug\Requests\Catalog;

use FredBradley\SmugMug\Data\Commerce\CatalogSku;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ItemRequest;
use FredBradley\SmugMug\Support\Path;

/**
 * A SKU, by id: GET /api/v2/catalog/sku/{skuId}.
 */
class GetCatalogSkuRequest extends ItemRequest
{
    protected ?string $dto = CatalogSku::class;

    /**
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $skuId, Options|array $options = [])
    {
        $this->setOptions($options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/catalog/sku/'.Path::segment($this->skuId);
    }
}
