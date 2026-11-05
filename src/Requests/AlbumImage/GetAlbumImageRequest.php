<?php

namespace FredBradley\SmugMug\Requests\AlbumImage;

use FredBradley\SmugMug\Data\Album\AlbumImage;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ItemRequest;
use FredBradley\SmugMug\Support\Path;

/**
 * An image as it is in one album: GET /api/v2/album/{albumKey}/image/{imageKey}.
 */
class GetAlbumImageRequest extends ItemRequest
{
    protected ?string $dto = AlbumImage::class;

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
