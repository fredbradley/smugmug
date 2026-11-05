<?php

namespace FredBradley\SmugMug\Requests\Album;

use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ActionRequest;
use FredBradley\SmugMug\Support\Path;
use JsonSerializable;

/**
 * Move images into an album from other albums: POST /api/v2/album/{albumKey}!moveimages.
 *
 * Parameters (in the body):
 *  - MoveUris (required): the album images to move here (a list of album image URIs)
 *  - Async: true to do it in the background
 */
class MoveAlbumImagesRequest extends ActionRequest
{
    protected array $commaSeparated = ['MoveUris'];

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
        return '/api/v2/album/'.Path::segment($this->albumKey).'!moveimages';
    }
}
