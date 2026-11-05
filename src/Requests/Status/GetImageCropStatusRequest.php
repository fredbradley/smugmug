<?php

namespace FredBradley\SmugMug\Requests\Status;

use FredBradley\SmugMug\Data\Image\ImageEditStatus;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ItemRequest;
use FredBradley\SmugMug\Support\Path;

/**
 * The progress of a crop: GET /api/v2/status/image/crop/{statusId}.
 */
class GetImageCropStatusRequest extends ItemRequest
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
        return '/api/v2/status/image/crop/'.Path::segment($this->statusId);
    }
}
