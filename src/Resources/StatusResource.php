<?php

namespace FredBradley\SmugMug\Resources;

use FredBradley\SmugMug\Data\Image\ImageEditStatus;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\Status\GetImageColorStatusRequest;
use FredBradley\SmugMug\Requests\Status\GetImageCropStatusRequest;
use FredBradley\SmugMug\Requests\Status\GetImageRotateStatusRequest;
use FredBradley\SmugMug\Requests\Status\GetImageWatermarkStatusRequest;

/**
 * The progress of asynchronous image edits, from SmugMug::status().
 */
class StatusResource extends BaseResource
{
    /**
     * The progress of a rotation: GET /api/v2/status/image/rotate/{statusId}.
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function rotation(string $statusId, Options|array $options = []): ?ImageEditStatus
    {
        return $this->fetch(new GetImageRotateStatusRequest($statusId, $this->options($options)));
    }

    /**
     * The progress of a crop: GET /api/v2/status/image/crop/{statusId}.
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function crop(string $statusId, Options|array $options = []): ?ImageEditStatus
    {
        return $this->fetch(new GetImageCropStatusRequest($statusId, $this->options($options)));
    }

    /**
     * The progress of a colour effect: GET /api/v2/status/image/color/{statusId}.
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function color(string $statusId, Options|array $options = []): ?ImageEditStatus
    {
        return $this->fetch(new GetImageColorStatusRequest($statusId, $this->options($options)));
    }

    /**
     * The progress of a watermarking: GET /api/v2/status/image/watermark/{statusId}.
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function watermark(string $statusId, Options|array $options = []): ?ImageEditStatus
    {
        return $this->fetch(new GetImageWatermarkStatusRequest($statusId, $this->options($options)));
    }
}
