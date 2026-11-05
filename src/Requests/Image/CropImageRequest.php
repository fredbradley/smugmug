<?php

namespace FredBradley\SmugMug\Requests\Image;

use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ActionRequest;
use FredBradley\SmugMug\Support\Path;
use JsonSerializable;

/**
 * Crop an image: POST /api/v2/image/{imageKey}!crop.
 *
 * Parameters (in the body):
 *  - TopLeftX (required), TopLeftY (required), Width (required), Height (required): the crop, in pixels of the original
 *  - ThumbOnly (required): true to crop only the thumbnail
 */
class CropImageRequest extends ActionRequest
{
    /**
     * @param  array<mixed>|JsonSerializable  $body  SmugMug's field names and values, or a model.
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $imageKey, array|JsonSerializable $body = [], Options|array $options = [])
    {
        $this->setBody($body, $options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/image/'.Path::segment($this->imageKey).'!crop';
    }
}
