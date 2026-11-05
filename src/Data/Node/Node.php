<?php

namespace FredBradley\SmugMug\Data\Node;

use Carbon\CarbonImmutable;
use FredBradley\SmugMug\Data\Data;

/**
 * A folder, album or page in a user's site, by its node id: the hierarchy of New SmugMug.
 *
 * SmugMug sends it as: Node, RootNode.
 */
readonly class Node extends Data
{
    protected const HIDDEN = ['Password'];

    /**
     * @param  list<mixed>|null  $Keywords
     * @param  array<string, mixed>|null  $FormattedValues
     * @param  array<string, mixed>  $Uris  The related endpoints, by name (see uri()).
     * @param  array<string, mixed>  $extra  The fields SmugMug sent that the model has no property for.
     */
    public function __construct(
        public ?string $CoverImageUri = null,
        public ?string $Description = null,
        public ?bool $HideOwner = null,
        public ?string $HighlightImageUri = null,
        public ?string $Name = null,
        public ?array $Keywords = null,
        public ?string $Password = null,
        public ?string $PasswordHint = null,
        public ?string $Privacy = null,
        public ?string $SecurityType = null,
        public ?bool $ShowCoverImage = null,
        public ?string $SmugSearchable = null,
        public ?string $SortDirection = null,
        public ?string $SortMethod = null,
        public ?string $Type = null,
        public ?string $UrlName = null,
        public ?string $WorldSearchable = null,
        public ?bool $BelongsToThirdPartyAccountGroup = null,
        public ?CarbonImmutable $DateAdded = null,
        public ?CarbonImmutable $DateModified = null,
        public ?string $EffectivePrivacy = null,
        public ?string $EffectiveSecurityType = null,
        public ?array $FormattedValues = null,
        public ?bool $HasChildren = null,
        public ?bool $IsRoot = null,
        public ?string $NodeID = null,
        public ?int $SortIndex = null,
        public ?string $UrlPath = null,
        public ?string $Uri = null,
        public ?string $WebUri = null,
        public ?string $UriDescription = null,
        public ?string $ResponseLevel = null,
        public array $Uris = [],
        public array $extra = [],
    ) {}
}
