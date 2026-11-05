<?php

namespace FredBradley\SmugMug\Requests\Image;

use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ActionRequest;
use JsonSerializable;

/**
 * Crop several images the same way: POST /api/v2/image!crop.
 *
 * Parameters (in the body):
 *  - CropUris (required): the images (a list of URIs)
 *  - TopLeftX (required), TopLeftY (required), Width (required), Height (required): the crop
 *  - ThumbOnly (required): true to crop only the thumbnails
 */
class CropImagesRequest extends ActionRequest
{
    protected array $commaSeparated = ['CropUris'];

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
        return '/api/v2/image!crop';
    }
}
