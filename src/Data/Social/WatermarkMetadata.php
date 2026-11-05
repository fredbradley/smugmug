<?php

namespace FredBradley\SmugMug\Data\Social;

use FredBradley\SmugMug\Data\Data;

/**
 * The text of a text watermark: contents, colours, font and size.
 *
 * SmugMug sends it as: WatermarkMetadata.
 */
readonly class WatermarkMetadata extends Data
{
    /**
     * @param  array<string, mixed>  $Uris  The related endpoints, by name (see uri()).
     * @param  array<string, mixed>  $extra  The fields SmugMug sent that the model has no property for.
     */
    public function __construct(
        public ?string $TextContents = null,
        public ?string $TextColor = null,
        public ?string $TextBackgroundColor = null,
        public ?string $TextFont = null,
        public ?int $TextSize = null,
        public ?string $Uri = null,
        public ?string $UriDescription = null,
        public array $Uris = [],
        public array $extra = [],
    ) {}
}
