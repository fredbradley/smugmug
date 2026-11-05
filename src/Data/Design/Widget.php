<?php

namespace FredBradley\SmugMug\Data\Design;

use FredBradley\SmugMug\Data\Data;

/**
 * A widget placed on a page design.
 *
 * SmugMug sends it as: PublishedPageDesignWidgets, WorkingPageDesignWidgets, ProfileWidget.
 */
readonly class Widget extends Data
{
    /**
     * @param  array<string, mixed>  $Uris  The related endpoints, by name (see uri()).
     * @param  array<string, mixed>  $extra  The fields SmugMug sent that the model has no property for.
     */
    public function __construct(
        public ?string $Type = null,
        public ?string $Uri = null,
        public ?string $UriDescription = null,
        public array $Uris = [],
        public array $extra = [],
    ) {}
}
