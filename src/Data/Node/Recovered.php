<?php

namespace FredBradley\SmugMug\Data\Node;

use FredBradley\SmugMug\Data\Data;

/**
 * The result of recovering a deleted album, folder or page.
 *
 * SmugMug sends it as: RecoverDeletedAlbum, RecoverDeletedFolder, RecoverDeletedPage.
 */
readonly class Recovered extends Data
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
