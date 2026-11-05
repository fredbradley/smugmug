<?php

namespace FredBradley\SmugMug\Requests\Image;

use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ActionRequest;
use JsonSerializable;

/**
 * Add keywords to several images: POST /api/v2/image!addkeywords.
 *
 * Parameters (in the body):
 *  - ImageUris (required): the images (a list of URIs)
 *  - Keywords (required): the keywords to add (semicolon-separated)
 *  - Async: true to do it in the background
 */
class AddImageKeywordsRequest extends ActionRequest
{
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
        return '/api/v2/image!addkeywords';
    }
}
