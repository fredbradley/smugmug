<?php

namespace FredBradley\SmugMug\Data\Image;

use FredBradley\SmugMug\Data\Data;

/**
 * The progress of an asynchronous colour, crop, rotate or watermark operation.
 *
 * SmugMug sends it as: StatusImageColor, StatusImageCrop, StatusImageRotate, StatusImageWatermark.
 */
readonly class ImageEditStatus extends Data
{
    /**
     * @param  array<string, mixed>  $Uris  The related endpoints, by name (see uri()).
     * @param  array<string, mixed>  $extra  The fields SmugMug sent that the model has no property for.
     */
    public function __construct(
        public ?string $Uri = null,
        public ?string $UriDescription = null,
        public array $Uris = [],
        public array $extra = [],
    ) {}
}
