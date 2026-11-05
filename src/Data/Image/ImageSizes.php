<?php

namespace FredBradley\SmugMug\Data\Image;

use FredBradley\SmugMug\Data\Data;

/**
 * The URL of each size of an image (and video) SmugMug serves.
 *
 * SmugMug sends it as: ImageSizes.
 *
 * Deprecated by SmugMug, kept for older objects: 960VideoUrl.
 */
readonly class ImageSizes extends Data
{
    protected const ALIASES = [
        'ImageUrl4K' => '4KImageUrl',
        'ImageUrl5K' => '5KImageUrl',
        'VideoUrl1920' => '1920VideoUrl',
        'VideoUrl1280' => '1280VideoUrl',
        'VideoUrl960' => '960VideoUrl',
        'VideoUrl640' => '640VideoUrl',
        'VideoUrl320' => '320VideoUrl',
        'VideoUrl200' => '200VideoUrl',
        'VideoUrl110' => '110VideoUrl',
    ];

    /**
     * @param  array<string, mixed>  $Uris  The related endpoints, by name (see uri()).
     * @param  array<string, mixed>  $extra  The fields SmugMug sent that the model has no property for.
     */
    public function __construct(
        public ?string $LargestImageUrl = null,
        public ?string $LargestVideoUrl = null,
        public ?string $TinyImageUrl = null,
        public ?string $ThumbImageUrl = null,
        public ?string $SmallImageUrl = null,
        public ?string $MediumImageUrl = null,
        public ?string $LargeImageUrl = null,
        public ?string $XLargeImageUrl = null,
        public ?string $X2LargeImageUrl = null,
        public ?string $X3LargeImageUrl = null,
        public ?string $X4LargeImageUrl = null,
        public ?string $X5LargeImageUrl = null,
        public ?string $ImageUrl4K = null,
        public ?string $ImageUrl5K = null,
        public ?string $OriginalImageUrl = null,
        public ?string $VideoUrl1920 = null,
        public ?string $VideoUrl1280 = null,
        public ?string $VideoUrl960 = null,
        public ?string $VideoUrl640 = null,
        public ?string $VideoUrl320 = null,
        public ?string $VideoUrl200 = null,
        public ?string $VideoUrl110 = null,
        public ?string $SMILVideoUrl = null,
        public ?string $Uri = null,
        public ?string $UriDescription = null,
        public array $Uris = [],
        public array $extra = [],
    ) {}
}
