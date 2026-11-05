<?php

namespace FredBradley\SmugMug\Requests\Watermark;

use FredBradley\SmugMug\Data\Social\WatermarkMetadata;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\WriteRequest;
use FredBradley\SmugMug\Support\Path;
use JsonSerializable;
use Saloon\Enums\Method;

/**
 * Change the text of a text watermark: PATCH /api/v2/watermark/{watermarkId}!metadata.
 *
 * Parameters (in the body):
 *  - TextContents, TextColor, TextBackgroundColor, TextFont, TextSize
 */
class UpdateWatermarkMetadataRequest extends WriteRequest
{
    protected Method $method = Method::PATCH;

    protected ?string $dto = WatermarkMetadata::class;

    /**
     * @param  array<mixed>|JsonSerializable  $body  SmugMug's field names and values, or a model.
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $watermarkId, array|JsonSerializable $body = [], Options|array $options = [])
    {
        $this->setBody($body, $options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/watermark/'.Path::segment($this->watermarkId).'!metadata';
    }
}
