<?php

namespace FredBradley\SmugMug\Data\Image;

use FredBradley\SmugMug\Data\Data;

/**
 * The point of an image crops centre on, such as a face.
 *
 * SmugMug sends it as: PointOfInterest.
 */
readonly class PointOfInterest extends Data
{
    /**
     * @param  array<string, mixed>  $Uris  The related endpoints, by name (see uri()).
     * @param  array<string, mixed>  $extra  The fields SmugMug sent that the model has no property for.
     */
    public function __construct(
        public ?float $Width = null,
        public ?float $Height = null,
        public ?float $X = null,
        public ?float $Y = null,
        public ?string $Type = null,
        public ?string $Uri = null,
        public ?string $UriDescription = null,
        public array $Uris = [],
        public array $extra = [],
    ) {}
}
