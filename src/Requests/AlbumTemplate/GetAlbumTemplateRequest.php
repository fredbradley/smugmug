<?php

namespace FredBradley\SmugMug\Requests\AlbumTemplate;

use FredBradley\SmugMug\Data\Album\AlbumTemplate;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ItemRequest;
use FredBradley\SmugMug\Support\Path;

/**
 * An album template, by id: GET /api/v2/albumtemplate/{albumTemplateId}.
 */
class GetAlbumTemplateRequest extends ItemRequest
{
    protected ?string $dto = AlbumTemplate::class;

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
