<?php

namespace FredBradley\SmugMug\Data\Album;

use FredBradley\SmugMug\Data\Data;

/**
 * Where to download an image's original file.
 *
 * SmugMug sends it as: AlbumImageDownload, ImageDownload.
 */
readonly class DownloadUrl extends Data
{
    /**
     * @param  array<string, mixed>  $Uris  The related endpoints, by name (see uri()).
     * @param  array<string, mixed>  $extra  The fields SmugMug sent that the model has no property for.
     */
    public function __construct(
        public ?string $Url = null,
        public ?string $Uri = null,
        public ?string $UriDescription = null,
        public array $Uris = [],
        public array $extra = [],
    ) {}
}
