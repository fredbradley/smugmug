<?php

namespace FredBradley\SmugMug\Requests\Album;

use FredBradley\SmugMug\Data\Album\AppliedAlbumTemplate;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\WriteRequest;
use FredBradley\SmugMug\Support\Path;
use JsonSerializable;

/**
 * Apply an album template's settings to an album: POST /api/v2/album/{albumKey}!applyalbumtemplate.
 *
 * Parameters (in the body):
 *  - AlbumTemplateUri (required): the album template to apply
 */
class ApplyAlbumTemplateRequest extends WriteRequest
{
    protected ?string $dto = AppliedAlbumTemplate::class;

    /**
     * @param  array<mixed>|JsonSerializable  $body  SmugMug's field names and values, or a model.
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $albumKey, array|JsonSerializable $body = [], Options|array $options = [])
    {
        $this->setBody($body, $options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/album/'.Path::segment($this->albumKey).'!applyalbumtemplate';
    }
}
