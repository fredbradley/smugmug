<?php

namespace FredBradley\SmugMug\Data\User;

use FredBradley\SmugMug\Data\Data;

/**
 * A user's site-wide settings.
 *
 * SmugMug sends it as: SiteSettings.
 */
readonly class SiteSettings extends Data
{
    /**
     * @param  array<string, mixed>  $Uris  The related endpoints, by name (see uri()).
     * @param  array<string, mixed>  $extra  The fields SmugMug sent that the model has no property for.
     */
    public function __construct(
        public ?string $RightClickMessage = null,
        public ?string $DefaultTemplateUri = null,
        public ?string $Uri = null,
        public ?string $UriDescription = null,
        public array $Uris = [],
        public array $extra = [],
    ) {}
}
