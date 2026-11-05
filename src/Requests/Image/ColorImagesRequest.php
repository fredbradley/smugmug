<?php

namespace FredBradley\SmugMug\Requests\Image;

use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ActionRequest;
use JsonSerializable;

/**
 * Apply a colour effect to several images: POST /api/v2/image!color.
 *
 * Parameters (in the body):
 *  - ColorUris (required): the images (a list of URIs)
 *  - Color (required): Auto, Tan, Sepia, B/W or Negative
 */
class ColorImagesRequest extends ActionRequest
{
    protected array $commaSeparated = ['ColorUris'];

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
        return '/api/v2/image!color';
    }
}
