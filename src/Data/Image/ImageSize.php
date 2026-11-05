<?php

namespace FredBradley\SmugMug\Data\Image;

use FredBradley\SmugMug\Data\Data;

/**
 * One size of an image: its URL, dimensions, file size and MD5.
 *
 * SmugMug sends it as: ImageSizeTiny, ImageSizeThumb, ImageSizeSmall, ImageSizeMedium, ImageSizeLarge, ImageSizeXLarge, ImageSizeX2Large, ImageSizeX3Large, ImageSizeX4Large, ImageSizeX5Large, ImageSize4K, ImageSize5K, ImageSizeOriginal, LargestImage, ImageSizeCustom.
 */
readonly class ImageSize extends Data
{
    /**
     * @param  array<string, mixed>  $Uris  The related endpoints, by name (see uri()).
     * @param  array<string, mixed>  $extra  The fields SmugMug sent that the model has no property for.
     */
    public function __construct(
        public ?string $Url = null,
        public ?int $Size = null,
        public ?int $Height = null,
        public ?int $Width = null,
        public ?string $MD5 = null,
        public ?bool $Usable = null,
        public ?string $Ext = null,
        public ?float $Duration = null,
        public ?float $Bitrate = null,
        public ?bool $Watermarked = null,
        public ?int $RequestedHeight = null,
        public ?int $RequestedWidth = null,
        public ?bool $RequestedFill = null,
        public ?string $Uri = null,
        public ?string $UriDescription = null,
        public array $Uris = [],
        public array $extra = [],
    ) {}
}
