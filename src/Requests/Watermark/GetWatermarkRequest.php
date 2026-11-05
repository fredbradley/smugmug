<?php

namespace FredBradley\SmugMug\Requests\Watermark;

use FredBradley\SmugMug\Data\Social\Watermark;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ItemRequest;
use FredBradley\SmugMug\Support\Path;

/**
 * A watermark, by id: GET /api/v2/watermark/{watermarkId}.
 */
class GetWatermarkRequest extends ItemRequest
{
    protected ?string $dto = Watermark::class;

    /**
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $watermarkId, Options|array $options = [])
    {
        $this->setOptions($options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/watermark/'.Path::segment($this->watermarkId);
    }
}
