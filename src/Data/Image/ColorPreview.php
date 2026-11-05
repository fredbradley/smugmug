<?php

namespace FredBradley\SmugMug\Data\Image;

use FredBradley\SmugMug\Data\Data;

/**
 * The size of an image after a colour effect.
 *
 * SmugMug sends it as: ColorImage.
 */
readonly class ColorPreview extends Data
{
    /**
     * @param  array<string, mixed>  $Uris  The related endpoints, by name (see uri()).
     * @param  array<string, mixed>  $extra  The fields SmugMug sent that the model has no property for.
     */
    public function __construct(
        public ?int $Width = null,
        public ?int $Height = null,
        public ?string $Uri = null,
        public ?string $UriDescription = null,
        public array $Uris = [],
        public array $extra = [],
    ) {}
}
