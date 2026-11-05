<?php

namespace FredBradley\SmugMug\Data\Design;

use Carbon\CarbonImmutable;
use FredBradley\SmugMug\Data\Data;

/**
 * A design (theme) for the user's site.
 *
 * SmugMug sends it as: SiteDesign, RootSiteDesign.
 */
readonly class SiteDesign extends Data
{
    /**
     * @param  array<string, mixed>  $Uris  The related endpoints, by name (see uri()).
     * @param  array<string, mixed>  $extra  The fields SmugMug sent that the model has no property for.
     */
    public function __construct(
        public ?string $DesignPickerDescription = null,
        public ?int $DesignPickerOrder = null,
        public ?string $DesignPickerGroupName = null,
        public ?string $DesignPickerPreviewUrl = null,
        public ?string $DesignPickerPreviewThumbnailUrl = null,
        public ?string $FooterDisplay = null,
        public ?bool $HeaderDisplay = null,
        public ?string $Name = null,
        public ?string $PoweredByAlignment = null,
        public ?string $PreviewNickName = null,
        public ?string $SiteDesignKey = null,
        public ?string $Status = null,
        public ?bool $Global = null,
        public ?bool $UniqueHomepage = null,
        public ?bool $HasUnpublishedChanges = null,
        public ?CarbonImmutable $DateModified = null,
        public ?string $Uri = null,
        public ?string $UriDescription = null,
        public array $Uris = [],
        public array $extra = [],
    ) {}
}
