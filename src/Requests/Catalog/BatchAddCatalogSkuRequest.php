<?php

namespace FredBradley\SmugMug\Requests\Catalog;

use FredBradley\SmugMug\Data\Commerce\BatchAddResult;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\WriteRequest;
use FredBradley\SmugMug\Support\Path;
use JsonSerializable;

/**
 * Add a SKU to the cart for many images at once: POST /api/v2/catalog/sku/{skuId}!batchadd.
 *
 * Parameters (in the body):
 *  - Images (required): the images (a list of URIs)
 *  - Quantity (required)
 *  - FilterImagesOverTheCartItemLimit
 */
class BatchAddCatalogSkuRequest extends WriteRequest
{
    protected ?string $dto = BatchAddResult::class;

    /**
     * @param  array<mixed>|JsonSerializable  $body  SmugMug's field names and values, or a model.
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $skuId, array|JsonSerializable $body = [], Options|array $options = [])
    {
        $this->setBody($body, $options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/catalog/sku/'.Path::segment($this->skuId).'!batchadd';
    }
}
