<?php

namespace FredBradley\SmugMug\Requests\Album;

use FredBradley\SmugMug\Data\Album\Album;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ListRequest;
use FredBradley\SmugMug\Support\Path;

/**
 * Several albums at once (multi-get), by key: GET /api/v2/album/{albumKeys}.
 */
class GetAlbumsRequest extends ListRequest
{
    protected ?string $dto = Album::class;

    /**
     * @param  string|list<string>  $albumKeys
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string|array $albumKeys, Options|array $options = [])
    {
        $this->setOptions($options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/album/'.Path::segment($this->albumKeys);
    }
}
