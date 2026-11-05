<?php

namespace FredBradley\SmugMug\Requests\Album;

use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ActionRequest;
use FredBradley\SmugMug\Support\Path;
use JsonSerializable;

/**
 * Collect images from other albums into an album (they stay where they are too): POST /api/v2/album/{albumKey}!collectimages.
 *
 * Parameters (in the body):
 *  - CollectUris (required): the images to collect (a list of image URIs)
 *  - Async: true to do it in the background
 */
class CollectAlbumImagesRequest extends ActionRequest
{
    protected array $commaSeparated = ['CollectUris'];

    /**
     * @param  array<mixed>|JsonSerializable  $body  SmugMug's field names and values, or a model.
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $albumKey, array|JsonSerializable $body = [], Options|array $options = [])
    {
        $this->setBody($body, $options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/album/'.Path::segment($this->albumKey).'!collectimages';
    }
}
