<?php

namespace FredBradley\SmugMug\Data\Image;

use FredBradley\SmugMug\Data\Data;

/**
 * Every size of an image in detail: URL, dimensions, file size and whether it may be used.
 *
 * SmugMug sends it as: ImageSizeDetails.
 *
 * Deprecated by SmugMug, kept for older objects: ImageUrlTemplate, VideoUrlTemplate, VideoSize960.
 */
readonly class ImageSizeDetails extends Data
{
    /**
     * @param  list<mixed>|null  $UsableSizes
     * @param  array<string, mixed>|null  $ImageSizeTiny
     * @param  array<string, mixed>|null  $ImageSizeThumb
     * @param  array<string, mixed>|null  $ImageSizeSmall
     * @param  array<string, mixed>|null  $ImageSizeMedium
     * @param  array<string, mixed>|null  $ImageSizeLarge
     * @param  array<string, mixed>|null  $ImageSizeXLarge
     * @param  array<string, mixed>|null  $ImageSizeX2Large
     * @param  array<string, mixed>|null  $ImageSizeX3Large
     * @param  array<string, mixed>|null  $ImageSizeX4Large
     * @param  array<string, mixed>|null  $ImageSizeX5Large
     * @param  array<string, mixed>|null  $ImageSize4K
     * @param  array<string, mixed>|null  $ImageSize5K
     * @param  array<string, mixed>|null  $ImageSizeOriginal
     * @param  array<string, mixed>|null  $VideoSize1920
     * @param  array<string, mixed>|null  $VideoSize1280
     * @param  array<string, mixed>|null  $VideoSize960
     * @param  array<string, mixed>|null  $VideoSize640
     * @param  array<string, mixed>|null  $VideoSize320
     * @param  array<string, mixed>|null  $VideoSize200
     * @param  array<string, mixed>|null  $VideoSize110
     * @param  array<string, mixed>|null  $VideoSMIL
     * @param  array<string, mixed>  $Uris  The related endpoints, by name (see uri()).
     * @param  array<string, mixed>  $extra  The fields SmugMug sent that the model has no property for.
     */
    public function __construct(
        public ?string $ImageUrlTemplate = null,
        public ?string $VideoUrlTemplate = null,
        public ?array $UsableSizes = null,
        public ?array $ImageSizeTiny = null,
        public ?array $ImageSizeThumb = null,
        public ?array $ImageSizeSmall = null,
        public ?array $ImageSizeMedium = null,
        public ?array $ImageSizeLarge = null,
        public ?array $ImageSizeXLarge = null,
        public ?array $ImageSizeX2Large = null,
        public ?array $ImageSizeX3Large = null,
        public ?array $ImageSizeX4Large = null,
        public ?array $ImageSizeX5Large = null,
        public ?array $ImageSize4K = null,
        public ?array $ImageSize5K = null,
        public ?array $ImageSizeOriginal = null,
        public ?array $VideoSize1920 = null,
        public ?array $VideoSize1280 = null,
        public ?array $VideoSize960 = null,
        public ?array $VideoSize640 = null,
        public ?array $VideoSize320 = null,
        public ?array $VideoSize200 = null,
        public ?array $VideoSize110 = null,
        public ?array $VideoSMIL = null,
        public ?string $LargestImageSize = null,
        public ?string $Uri = null,
        public ?string $UriDescription = null,
        public array $Uris = [],
        public array $extra = [],
    ) {}
}
