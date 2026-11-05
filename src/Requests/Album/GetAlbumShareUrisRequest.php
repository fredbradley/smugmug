<?php

namespace FredBradley\SmugMug\Requests\Album;

use FredBradley\SmugMug\Data\Album\ShareUris;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ItemRequest;
use FredBradley\SmugMug\Support\Path;

/**
 * The links to share an album: GET /api/v2/album/{albumKey}!shareuris.
 */
class GetAlbumShareUrisRequest extends ItemRequest
{
    protected ?string $dto = ShareUris::class;

    /**
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $albumKey, Options|array $options = [])
    {
        $this->setOptions($options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/album/'.Path::segment($this->albumKey).'!shareuris';
    }
}
