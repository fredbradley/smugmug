<?php

namespace FredBradley\SmugMug\Data\Node;

use Carbon\CarbonImmutable;
use FredBradley\SmugMug\Data\Data;

/**
 * An album, folder or page in the user's trash, which can be recovered.
 *
 * SmugMug sends it as: DeletedAlbum, DeletedFolder, DeletedPage.
 */
readonly class DeletedNode extends Data
{
    /**
     * @param  array<string, mixed>  $Uris  The related endpoints, by name (see uri()).
     * @param  array<string, mixed>  $extra  The fields SmugMug sent that the model has no property for.
     */
    public function __construct(
        public ?string $UrlPath = null,
        public ?string $Name = null,
        public ?CarbonImmutable $DateAdded = null,
        public ?CarbonImmutable $DateDeleted = null,
        public ?CarbonImmutable $DateModified = null,
        public ?string $Uri = null,
        public ?string $UriDescription = null,
        public array $Uris = [],
        public array $extra = [],
    ) {}
}
