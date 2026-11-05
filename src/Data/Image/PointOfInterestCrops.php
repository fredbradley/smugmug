<?php

namespace FredBradley\SmugMug\Data\Image;

use FredBradley\SmugMug\Data\Data;

/**
 * The crops of an image around its point of interest.
 *
 * SmugMug sends it as: PointOfInterestCrops.
 */
readonly class PointOfInterestCrops extends Data
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
