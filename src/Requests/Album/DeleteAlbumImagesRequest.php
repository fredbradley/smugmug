<?php

namespace FredBradley\SmugMug\Requests\Album;

use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ActionRequest;
use FredBradley\SmugMug\Support\Path;
use JsonSerializable;

/**
 * Delete several images from an album at once: POST /api/v2/album/{albumKey}!deleteimages.
 *
 * Parameters (in the body):
 *  - DeleteUris (required): the album images to delete (a list of URIs)
 *  - Async: true to do it in the background
 */
class DeleteAlbumImagesRequest extends ActionRequest
{
    protected array $commaSeparated = ['DeleteUris'];

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
        return '/api/v2/album/'.Path::segment($this->albumKey).'!deleteimages';
    }
}
