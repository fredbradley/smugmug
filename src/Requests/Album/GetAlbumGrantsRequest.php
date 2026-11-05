<?php

namespace FredBradley\SmugMug\Requests\Album;

use FredBradley\SmugMug\Data\User\Grant;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ListRequest;
use FredBradley\SmugMug\Support\Path;

/**
 * Who has been granted access to an album: GET /api/v2/album/{albumKey}!grants.
 */
class GetAlbumGrantsRequest extends ListRequest
{
    protected ?string $dto = Grant::class;

    /**
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $albumKey, Options|array $options = [])
    {
        $this->setOptions($options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/album/'.Path::segment($this->albumKey).'!grants';
    }
}
