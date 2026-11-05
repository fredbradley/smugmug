<?php

namespace FredBradley\SmugMug\Requests\Image;

use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ActionRequest;
use JsonSerializable;

/**
 * Watermark several images: POST /api/v2/image!watermark.
 *
 * Parameters (in the body):
 *  - WatermarkUris (required): the images (a list of URIs)
 *  - WatermarkUri: the watermark to apply
 *  - Mode: Add or Remove
 */
class WatermarkImagesRequest extends ActionRequest
{
    protected array $commaSeparated = ['WatermarkUris'];

    /**
     * @param  array<mixed>|JsonSerializable  $body  SmugMug's field names and values, or a model.
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(array|JsonSerializable $body = [], Options|array $options = [])
    {
        $this->setBody($body, $options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/image!watermark';
    }
}
