<?php

namespace FredBradley\SmugMug\Requests\Album;

use FredBradley\SmugMug\Data\Album\AlbumImage;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ListRequest;
use FredBradley\SmugMug\Support\Path;

/**
 * An album's images that carry a location: GET /api/v2/album/{albumKey}!geomedia.
 *
 * Parameters (in the options):
 *  - SortMethod: Popular, Oldest or Newest
 *  - MediaType: All, Images or Videos
 */
class GetAlbumGeoMediaRequest extends ListRequest
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
        return '/api/v2/album/'.Path::segment($this->albumKey).'!geomedia';
    }
}
