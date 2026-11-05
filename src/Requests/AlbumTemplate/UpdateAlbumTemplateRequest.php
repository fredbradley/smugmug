<?php

namespace FredBradley\SmugMug\Requests\AlbumTemplate;

use FredBradley\SmugMug\Data\Album\AlbumTemplate;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\WriteRequest;
use FredBradley\SmugMug\Support\Path;
use JsonSerializable;
use Saloon\Enums\Method;

/**
 * Change an album template: PATCH /api/v2/albumtemplate/{albumTemplateId}.
 *
 * Parameters (in the body):
 *  - Name, IsDefault, and any album setting
 */
class UpdateAlbumTemplateRequest extends WriteRequest
{
    protected Method $method = Method::PATCH;

    protected ?string $dto = AlbumTemplate::class;

    /**
     * @param  array<mixed>|JsonSerializable  $body  SmugMug's field names and values, or a model.
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $albumTemplateId, array|JsonSerializable $body = [], Options|array $options = [])
    {
        $this->setBody($body, $options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/albumtemplate/'.Path::segment($this->albumTemplateId);
    }
}
