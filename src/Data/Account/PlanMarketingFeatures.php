<?php

namespace FredBradley\SmugMug\Data\Account;

use FredBradley\SmugMug\Data\Data;

/**
 * Which features of their plan the user has used.
 *
 * SmugMug sends it as: PlanMarketingFeatures.
 */
readonly class PlanMarketingFeatures extends Data
{
    /**
     * @param  array<string, mixed>  $Uris  The related endpoints, by name (see uri()).
     * @param  array<string, mixed>  $extra  The fields SmugMug sent that the model has no property for.
     */
    public function __construct(
        public ?bool $ChangedDesignOrThemes = null,
        public ?bool $HasCustomized = null,
        public ?bool $CustomHTML_CSS = null,
        public ?bool $CustomDomain = null,
        public ?bool $HasSitePassword = null,
        public ?bool $UsesGrantAccess = null,
        public ?bool $UsesRightClick = null,
        public ?bool $HasVideo = null,
        public ?bool $HasMadeSales = null,
        public ?bool $UsesEvents = null,
        public ?bool $AddedLogoToSite = null,
        public ?bool $CustomizedCart = null,
        public ?bool $RemoveFooter = null,
        public ?bool $UsesWatermarks = null,
        public ?bool $HasPricing = null,
        public ?bool $HasNodePricing = null,
        public ?bool $UsesPackages = null,
        public ?bool $UsesCoupons = null,
        public ?bool $UsesProjects = null,
        public ?bool $CustomizedPackaging = null,
        public ?bool $UsesIncludedCollabSeats = null,
        public ?string $Uri = null,
        public ?string $UriDescription = null,
        public array $Uris = [],
        public array $extra = [],
    ) {}
}
