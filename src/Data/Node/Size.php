<?php

namespace FredBradley\SmugMug\Data\Node;

use FredBradley\SmugMug\Data\Data;

/**
 * How much a folder holds: folders, albums, pages and media, and their size.
 *
 * SmugMug sends it as: Size.
 */
readonly class Size extends Data
{
    /**
     * @param  array<string, mixed>  $Uris  The related endpoints, by name (see uri()).
     * @param  array<string, mixed>  $extra  The fields SmugMug sent that the model has no property for.
     */
    public function __construct(
        public ?int $FolderCount = null,
        public ?int $AlbumCount = null,
        public ?int $PageCount = null,
        public ?int $MediaCount = null,
        public ?int $MediaSize = null,
        public ?int $DynamicMediaCount = null,
        public ?int $DynamicMediaSize = null,
        public ?string $Uri = null,
        public ?string $UriDescription = null,
        public array $Uris = [],
        public array $extra = [],
    ) {}
}
