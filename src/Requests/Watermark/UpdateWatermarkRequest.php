<?php

namespace FredBradley\SmugMug\Requests\Watermark;

use FredBradley\SmugMug\Data\Social\Watermark;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\WriteRequest;
use FredBradley\SmugMug\Support\Path;
use JsonSerializable;
use Saloon\Enums\Method;

/**
 * Change a watermark: PATCH /api/v2/watermark/{watermarkId}.
 *
 * Parameters (in the body):
 *  - Name, ImageID, ImageKey, Dissolve, Pinned, SmallestSize
 */
class UpdateWatermarkRequest extends WriteRequest
{
    protected Method $method = Method::PATCH;

    protected ?string $dto = Watermark::class;

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
        return '/api/v2/watermark/'.Path::segment($this->watermarkId);
    }
}
