<?php

namespace FredBradley\SmugMug\Data\Social;

use Carbon\CarbonImmutable;
use FredBradley\SmugMug\Data\Data;

/**
 * A watermark the user can apply to their images.
 *
 * SmugMug sends it as: Watermark.
 *
 * Deprecated by SmugMug, kept for older objects: Thumbs.
 */
readonly class Watermark extends Data
{
    /**
     * @param  array<string, mixed>  $Uris  The related endpoints, by name (see uri()).
     * @param  array<string, mixed>  $extra  The fields SmugMug sent that the model has no property for.
     */
    public function __construct(
        public ?string $Name = null,
        public ?int $ImageID = null,
        public ?int $Dissolve = null,
        public ?string $Pinned = null,
        public ?bool $Thumbs = null,
        public ?string $SmallestSize = null,
        public ?string $ImageKey = null,
        public ?int $WatermarkID = null,
        public ?CarbonImmutable $TimeUpdated = null,
        public ?string $Uri = null,
        public ?string $UriDescription = null,
        public array $Uris = [],
        public array $extra = [],
    ) {}
}
