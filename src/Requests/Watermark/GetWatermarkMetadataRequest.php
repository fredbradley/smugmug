<?php

namespace FredBradley\SmugMug\Requests\Watermark;

use FredBradley\SmugMug\Data\Social\WatermarkMetadata;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ItemRequest;
use FredBradley\SmugMug\Support\Path;

/**
 * The text of a text watermark: GET /api/v2/watermark/{watermarkId}!metadata.
 */
class GetWatermarkMetadataRequest extends ItemRequest
{
    protected ?string $dto = WatermarkMetadata::class;

    /**
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $watermarkId, Options|array $options = [])
    {
        $this->setOptions($options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/watermark/'.Path::segment($this->watermarkId).'!metadata';
    }
}
