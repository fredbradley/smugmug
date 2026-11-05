<?php

namespace FredBradley\SmugMug\Requests\AlbumTemplate;

use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\DeleteRequest;
use FredBradley\SmugMug\Support\Path;

/**
 * Delete an album template: DELETE /api/v2/albumtemplate/{albumTemplateId}.
 */
class DeleteAlbumTemplateRequest extends DeleteRequest
{
    /**
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $albumTemplateId, Options|array $options = [])
    {
        $this->setOptions($options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/albumtemplate/'.Path::segment($this->albumTemplateId);
    }
}
