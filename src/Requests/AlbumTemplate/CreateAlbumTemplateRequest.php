<?php

namespace FredBradley\SmugMug\Requests\AlbumTemplate;

use FredBradley\SmugMug\Data\Album\AlbumTemplate;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\WriteRequest;
use FredBradley\SmugMug\Support\Path;
use JsonSerializable;

/**
 * Create an album template: POST /api/v2/user/{nickname}!albumtemplates.
 *
 * Parameters (in the body):
 *  - Name (required)
 *  - and any album setting (Privacy, Watermark, AllowDownloads, SortMethod and so on)
 *  - IsDefault: use it for new albums
 */
class CreateAlbumTemplateRequest extends WriteRequest
{
    protected ?string $dto = AlbumTemplate::class;

    /**
     * @param  array<mixed>|JsonSerializable  $body  SmugMug's field names and values, or a model.
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $nickname, array|JsonSerializable $body = [], Options|array $options = [])
    {
        $this->setBody($body, $options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/user/'.Path::segment($this->nickname).'!albumtemplates';
    }
}
