<?php

namespace FredBradley\SmugMug\Data\Node;

use Carbon\CarbonImmutable;
use FredBradley\SmugMug\Data\Data;

/**
 * A folder by its path (the folder API, which nodes replace).
 *
 * SmugMug sends it as: Folder.
 */
readonly class Folder extends Data
{
    protected const HIDDEN = ['Password'];

    /**
     * @param  array<string, mixed>  $Uris  The related endpoints, by name (see uri()).
     * @param  array<string, mixed>  $extra  The fields SmugMug sent that the model has no property for.
     */
    public function __construct(
        public ?string $Name = null,
        public ?string $UrlName = null,
        public ?string $SecurityType = null,
        public ?string $SortMethod = null,
        public ?string $SortDirection = null,
        public ?string $Description = null,
        public ?string $Keywords = null,
        public ?string $Password = null,
        public ?string $PasswordHint = null,
        public ?string $Privacy = null,
        public ?string $SmugSearchable = null,
        public ?string $WorldSearchable = null,
        public ?string $HighlightImageUri = null,
        public ?CarbonImmutable $DateAdded = null,
        public ?CarbonImmutable $DateModified = null,
        public ?string $UrlPath = null,
        public ?string $NodeID = null,
        public ?bool $IsEmpty = null,
        public ?string $Uri = null,
        public ?string $WebUri = null,
        public ?string $UriDescription = null,
        public ?string $ResponseLevel = null,
        public array $Uris = [],
        public array $extra = [],
    ) {}
}
