<?php

namespace FredBradley\SmugMug\Requests\Album;

use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ActionRequest;
use FredBradley\SmugMug\Support\Path;
use JsonSerializable;

/**
 * Reorder an album's images (for SortMethod Position): POST /api/v2/album/{albumKey}!sortimages.
 *
 * Parameters (in the body):
 *  - MoveUris (required): the album images to move (a list of URIs)
 *  - MoveLocation (required): Before or After
 *  - Uri (required): the album image to move them before or after
 */
class SortAlbumImagesRequest extends ActionRequest
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
        return '/api/v2/album/'.Path::segment($this->albumKey).'!sortimages';
    }
}
