<?php

namespace FredBradley\SmugMug\Requests\Guide;

use FredBradley\SmugMug\Data\Social\Guide;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ItemRequest;
use FredBradley\SmugMug\Support\Path;

/**
 * One of SmugMug's guides, by id: GET /api/v2/guide/{guideId}.
 */
class GetGuideRequest extends ItemRequest
{
    protected ?string $dto = Guide::class;

    /**
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $guideId, Options|array $options = [])
    {
        $this->setOptions($options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/guide/'.Path::segment($this->guideId);
    }
}
