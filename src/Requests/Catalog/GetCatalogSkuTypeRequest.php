<?php

namespace FredBradley\SmugMug\Requests\Catalog;

use FredBradley\SmugMug\Data\Commerce\CatalogSkuType;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ItemRequest;
use FredBradley\SmugMug\Support\Path;

/**
 * A type of SKU, by type and code: GET /api/v2/catalog/skutype/{skuType}{typeCode*}.
 */
class GetCatalogSkuTypeRequest extends ItemRequest
{
    protected ?string $dto = CatalogSkuType::class;

    /**
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $skuType, public readonly string $typeCode, Options|array $options = [])
    {
        $this->setOptions($options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/catalog/skutype/'.Path::segment($this->skuType).Path::folder($this->typeCode);
    }
}
