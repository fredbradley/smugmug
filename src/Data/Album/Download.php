<?php

namespace FredBradley\SmugMug\Data\Album;

use FredBradley\SmugMug\Data\Data;

/**
 * A download of an album's originals, prepared as a zip.
 *
 * SmugMug sends it as: Download.
 */
readonly class Download extends Data
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
