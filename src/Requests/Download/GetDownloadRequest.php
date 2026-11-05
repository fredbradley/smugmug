<?php

namespace FredBradley\SmugMug\Requests\Download;

use FredBradley\SmugMug\Data\Album\Download;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ItemRequest;
use FredBradley\SmugMug\Support\Path;

/**
 * A prepared download, by id: GET /api/v2/download/{downloadId}.
 */
class GetDownloadRequest extends ItemRequest
{
    protected ?string $dto = Download::class;

    /**
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $downloadId, Options|array $options = [])
    {
        $this->setOptions($options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/download/'.Path::segment($this->downloadId);
    }
}
