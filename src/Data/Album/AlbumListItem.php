<?php

namespace FredBradley\SmugMug\Data\Album;

use FredBradley\SmugMug\Data\Data;

/**
 * One album in a folder's flat album list.
 *
 * SmugMug sends it as: AlbumList.
 */
readonly class AlbumListItem extends Data
{
    /**
     * @param  array<string, mixed>  $Uris  The related endpoints, by name (see uri()).
     * @param  array<string, mixed>  $extra  The fields SmugMug sent that the model has no property for.
     */
    public function __construct(
        public ?string $Uri = null,
        public ?string $UrlPath = null,
        public ?string $Name = null,
        public array $Uris = [],
        public array $extra = [],
    ) {}
}
