<?php

namespace FredBradley\SmugMug\Requests\AlbumImage;

use FredBradley\SmugMug\Data\Image\ImageMetadata;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ItemRequest;
use FredBradley\SmugMug\Support\Path;

/**
 * An album image's EXIF, IPTC and XMP metadata: GET /api/v2/album/{albumKey}/image/{imageKey}!metadata.
 */
class GetAlbumImageMetadataRequest extends ItemRequest
{
    protected ?string $dto = ImageMetadata::class;

    /**
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $albumKey, public readonly string $imageKey, Options|array $options = [])
    {
        $this->setOptions($options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/album/'.Path::segment($this->albumKey).'/image/'.Path::segment($this->imageKey).'!metadata';
    }
}
