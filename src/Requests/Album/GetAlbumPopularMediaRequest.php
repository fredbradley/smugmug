<?php

namespace FredBradley\SmugMug\Requests\Album;

use FredBradley\SmugMug\Data\Album\AlbumImage;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ListRequest;
use FredBradley\SmugMug\Support\Path;

/**
 * An album's most viewed images: GET /api/v2/album/{albumKey}!popularmedia.
 *
 * Parameters (in the options):
 *  - MediaType: All, Images or Videos
 */
class GetAlbumPopularMediaRequest extends ListRequest
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
        return '/api/v2/album/'.Path::segment($this->albumKey).'!popularmedia';
    }
}
