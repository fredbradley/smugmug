<?php

namespace FredBradley\SmugMug\Requests\AlbumImage;

use FredBradley\SmugMug\Data\Album\DownloadUrl;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ItemRequest;
use FredBradley\SmugMug\Support\Path;

/**
 * Where to download an album image's original: GET /api/v2/album/{albumKey}/image/{imageKey}!download.
 */
class GetAlbumImageDownloadRequest extends ItemRequest
{
    protected ?string $dto = DownloadUrl::class;

    /**
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $albumKey, public readonly string $imageKey, Options|array $options = [])
    {
        $this->setOptions($options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/album/'.Path::segment($this->albumKey).'/image/'.Path::segment($this->imageKey).'!download';
    }
}
