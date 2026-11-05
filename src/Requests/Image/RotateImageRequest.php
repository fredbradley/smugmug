<?php

namespace FredBradley\SmugMug\Requests\Image;

use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ActionRequest;
use FredBradley\SmugMug\Support\Path;
use JsonSerializable;

/**
 * Rotate or flip an image: POST /api/v2/image/{imageKey}!rotate.
 *
 * Parameters (in the body):
 *  - Degrees (required): 0, 90, 180 or 270
 *  - Flip (required): None, Horizontal or Vertical
 */
class RotateImageRequest extends ActionRequest
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
        return '/api/v2/image/'.Path::segment($this->imageKey).'!rotate';
    }
}
