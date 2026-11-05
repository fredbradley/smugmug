<?php

namespace FredBradley\SmugMug\Requests\Catalog;

use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ActionRequest;
use FredBradley\SmugMug\Support\Path;
use JsonSerializable;

/**
 * Add a SKU to the cart: POST /api/v2/catalog/sku/{skuId}!buy.
 *
 * Parameters (in the body):
 *  - Quantity (required)
 *  - Image, Album: the image (and album) to buy it for
 *  - CropLeft, CropTop, CropWidth, CropHeight, CropRotation, CropAcknowledged: the crop
 *  - Source
 */
class BuyCatalogSkuRequest extends ActionRequest
{
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
        return '/api/v2/catalog/sku/'.Path::segment($this->skuId).'!buy';
    }
}
