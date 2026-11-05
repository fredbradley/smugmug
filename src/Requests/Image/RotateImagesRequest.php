<?php

namespace FredBradley\SmugMug\Requests\Image;

use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ActionRequest;
use JsonSerializable;

/**
 * Rotate or flip several images: POST /api/v2/image!rotate.
 *
 * Parameters (in the body):
 *  - RotateUris (required): the images (a list of URIs)
 *  - Degrees (required): 0, 90, 180 or 270
 *  - Flip (required): None, Horizontal or Vertical
 */
class RotateImagesRequest extends ActionRequest
{
    protected array $commaSeparated = ['RotateUris'];

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
        return '/api/v2/image!rotate';
    }
}
