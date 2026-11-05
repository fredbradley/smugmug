<?php

namespace FredBradley\SmugMug\Requests\Album;

use FredBradley\SmugMug\Data\Album\AlbumImage;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ListRequest;
use FredBradley\SmugMug\Support\Path;

/**
 * The images in an album: GET /api/v2/album/{albumKey}!images.
 *
 * Parameters (in the options):
 *  - FileNames: only these file names (comma-separated)
 *  - SkuId: only the images that may be sold as this SKU
 */
class GetAlbumImagesRequest extends ListRequest
{
    protected ?string $dto = AlbumImage::class;

    /**
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $albumKey, Options|array $options = [])
    {
        $this->setOptions($options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/album/'.Path::segment($this->albumKey).'!images';
    }
}
