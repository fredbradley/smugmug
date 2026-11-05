<?php

namespace FredBradley\SmugMug\Data\User;

use FredBradley\SmugMug\Data\Data;

/**
 * The feature flags SmugMug has set for the user.
 *
 * SmugMug sends it as: Flags.
 */
readonly class Flags extends Data
{
    /**
     * @param  array<string, mixed>|null  $Flags
     * @param  array<string, mixed>  $Uris  The related endpoints, by name (see uri()).
     * @param  array<string, mixed>  $extra  The fields SmugMug sent that the model has no property for.
     */
    public function __construct(
        public ?array $Flags = null,
        public ?string $Uri = null,
        public ?string $UriDescription = null,
        public array $Uris = [],
        public array $extra = [],
    ) {}
}
