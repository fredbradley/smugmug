<?php

namespace FredBradley\SmugMug\Data\Image;

use FredBradley\SmugMug\Data\Data;

/**
 * The products of the pricelist that may not be bought for an album or image.
 *
 * SmugMug sends it as: AlbumPricelistExclusions, ImagePricelistExclusions, AlbumImagePricelistExclusions.
 */
readonly class PricelistExclusions extends Data
{
    /**
     * @param  list<mixed>|null  $ExcludeList
     * @param  array<string, mixed>  $Uris  The related endpoints, by name (see uri()).
     * @param  array<string, mixed>  $extra  The fields SmugMug sent that the model has no property for.
     */
    public function __construct(
        public ?array $ExcludeList = null,
        public ?bool $ColorCorrected = null,
        public ?string $DiscountLevel = null,
        public ?string $Uri = null,
        public ?string $UriDescription = null,
        public ?string $ResponseLevel = null,
        public array $Uris = [],
        public array $extra = [],
    ) {}
}
