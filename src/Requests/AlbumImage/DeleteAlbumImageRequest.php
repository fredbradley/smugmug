<?php

namespace FredBradley\SmugMug\Requests\AlbumImage;

use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\DeleteRequest;
use FredBradley\SmugMug\Support\Path;

/**
 * Remove an image from an album (deleting it unless it is collected elsewhere): DELETE /api/v2/album/{albumKey}/image/{imageKey}.
 */
class DeleteAlbumImageRequest extends DeleteRequest
{
    /**
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $albumKey, public readonly string $imageKey, Options|array $options = [])
    {
        $this->setOptions($options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/album/'.Path::segment($this->albumKey).'/image/'.Path::segment($this->imageKey);
    }
}
