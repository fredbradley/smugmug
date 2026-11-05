<?php

namespace FredBradley\SmugMug\Data\Node;

use FredBradley\SmugMug\Data\Data;

/**
 * One folder in a folder's flat, recursive folder list.
 *
 * SmugMug sends it as: FolderList.
 */
readonly class FolderListItem extends Data
{
    /**
     * @param  array<string, mixed>  $Uris  The related endpoints, by name (see uri()).
     * @param  array<string, mixed>  $extra  The fields SmugMug sent that the model has no property for.
     */
    public function __construct(
        public ?string $Uri = null,
        public ?string $UrlPath = null,
        public ?string $Name = null,
        public ?bool $CanHaveFolder = null,
        public array $Uris = [],
        public array $extra = [],
    ) {}
}
