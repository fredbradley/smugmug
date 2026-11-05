<?php

namespace FredBradley\SmugMug\Data\Album;

use FredBradley\SmugMug\Data\Data;

/**
 * The album template applied to an album.
 *
 * SmugMug sends it as: ApplyAlbumTemplate.
 */
readonly class AppliedAlbumTemplate extends Data
{
    /**
     * @param  array<string, mixed>  $Uris  The related endpoints, by name (see uri()).
     * @param  array<string, mixed>  $extra  The fields SmugMug sent that the model has no property for.
     */
    public function __construct(
        public ?string $AlbumTemplateUri = null,
        public ?string $Uri = null,
        public ?string $UriDescription = null,
        public array $Uris = [],
        public array $extra = [],
    ) {}
}
