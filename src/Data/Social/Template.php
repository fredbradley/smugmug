<?php

namespace FredBradley\SmugMug\Data\Social;

use FredBradley\SmugMug\Data\Data;

/**
 * An album display template.
 *
 * SmugMug sends it as: Template.
 *
 * Deprecated by SmugMug, kept for older objects: CSSVer, Dark, Filename, Thumbs.
 */
readonly class Template extends Data
{
    /**
     * @param  array<string, mixed>  $Uris  The related endpoints, by name (see uri()).
     * @param  array<string, mixed>  $extra  The fields SmugMug sent that the model has no property for.
     */
    public function __construct(
        public ?bool $CSSVer = null,
        public ?bool $Dark = null,
        public ?string $Filename = null,
        public ?string $Name = null,
        public ?int $Thumbs = null,
        public ?string $Uri = null,
        public ?string $UriDescription = null,
        public array $Uris = [],
        public array $extra = [],
    ) {}
}
