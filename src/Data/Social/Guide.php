<?php

namespace FredBradley\SmugMug\Data\Social;

use FredBradley\SmugMug\Data\Data;

/**
 * One of SmugMug's in-app guides.
 *
 * SmugMug sends it as: Guide.
 */
readonly class Guide extends Data
{
    /**
     * @param  array<string, mixed>  $Uris  The related endpoints, by name (see uri()).
     * @param  array<string, mixed>  $extra  The fields SmugMug sent that the model has no property for.
     */
    public function __construct(
        public mixed $CreationDate = null,
        public mixed $GuideID = null,
        public ?string $Uri = null,
        public ?string $UriDescription = null,
        public array $Uris = [],
        public array $extra = [],
    ) {}
}
