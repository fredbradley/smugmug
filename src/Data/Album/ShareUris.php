<?php

namespace FredBradley\SmugMug\Data\Album;

use FredBradley\SmugMug\Data\Data;

/**
 * The links to share an album (its slideshow) or an album image (its lightbox and buy page).
 *
 * SmugMug sends it as: AlbumShareUris, AlbumImageShareUris.
 *
 * Deprecated by SmugMug, kept for older objects: SlideshowEmbedUri, SlideshowFlashUri.
 */
readonly class ShareUris extends Data
{
    /**
     * @param  array<string, mixed>  $Uris  The related endpoints, by name (see uri()).
     * @param  array<string, mixed>  $extra  The fields SmugMug sent that the model has no property for.
     */
    public function __construct(
        public ?string $SlideshowUri = null,
        public ?string $SlideshowEmbedUri = null,
        public ?string $SlideshowFlashUri = null,
        public ?string $LightboxUri = null,
        public ?string $BuyUri = null,
        public ?string $Uri = null,
        public ?string $UriDescription = null,
        public array $Uris = [],
        public array $extra = [],
    ) {}
}
