<?php

namespace FredBradley\SmugMug\Requests\Status;

use FredBradley\SmugMug\Data\Image\ImageEditStatus;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ItemRequest;
use FredBradley\SmugMug\Support\Path;

/**
 * The progress of a watermarking: GET /api/v2/status/image/watermark/{statusId}.
 */
class GetImageWatermarkStatusRequest extends ItemRequest
{
    protected ?string $dto = ImageEditStatus::class;

    /**
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $statusId, Options|array $options = [])
    {
        $this->setOptions($options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/status/image/watermark/'.Path::segment($this->statusId);
    }
}
