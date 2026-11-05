<?php

namespace FredBradley\SmugMug\Resources;

use FredBradley\SmugMug\Data\Album\Download;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\Download\GetDownloadRequest;

/**
 * Prepared album downloads, from SmugMug::downloads().
 */
class DownloadResource extends BaseResource
{
    /**
     * A prepared download, by id: GET /api/v2/download/{downloadId}.
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function get(string $downloadId, Options|array $options = []): ?Download
    {
        return $this->fetch(new GetDownloadRequest($downloadId, $this->options($options)));
    }
}
