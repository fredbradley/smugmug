<?php

namespace FredBradley\SmugMug\Data\User;

use FredBradley\SmugMug\Data\Data;

/**
 * What the user's plan lets them do: one flag per feature.
 *
 * SmugMug sends it as: Features.
 */
readonly class Features extends Data
{
    /**
     * @param  array<string, mixed>  $Uris  The related endpoints, by name (see uri()).
     * @param  array<string, mixed>  $extra  The fields SmugMug sent that the model has no property for.
     */
    public function __construct(
        public ?bool $Albums = null,
        public ?bool $Assistant = null,
        public ?bool $Backprinting = null,
        public ?bool $BasicCustomization = null,
        public ?bool $CameraConnections = null,
        public ?bool $ClientDownloads = null,
        public ?bool $Collaborators = null,
        public ?bool $Collecting = null,
        public ?bool $Coupon = null,
        public ?bool $CSS = null,
        public ?bool $CustomPackaging = null,
        public ?bool $Directory = null,
        public ?bool $Events = null,
        public ?bool $FrameIoIntegration = null,
        public ?bool $GalleryStats = null,
        public ?bool $GrantAccess = null,
        public ?bool $LegacyVault = null,
        public ?bool $MultiplePricelists = null,
        public ?bool $Packages = null,
        public ?bool $Pricing = null,
        public ?bool $Printmarks = null,
        public ?bool $Projects = null,
        public ?bool $PremiumStorage = null,
        public ?bool $QRCodes = null,
        public ?bool $RightClickProtection = null,
        public ?bool $Sales = null,
        public ?bool $Sharpening = null,
        public ?bool $SiteCustomization = null,
        public ?bool $Themes = null,
        public ?bool $Upload = null,
        public ?bool $Video = null,
        public ?bool $Watermarks = null,
        public ?string $Uri = null,
        public ?string $UriDescription = null,
        public array $Uris = [],
        public array $extra = [],
    ) {}
}
