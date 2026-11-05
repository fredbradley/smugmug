<?php

namespace FredBradley\SmugMug\Data\User;

use Carbon\CarbonImmutable;
use FredBradley\SmugMug\Data\Data;

/**
 * A SmugMug user: their account, site and contact details (some only for the owner).
 *
 * SmugMug sends it as: User.
 *
 * Deprecated by SmugMug, kept for older objects: Plan.
 */
readonly class User extends Data
{
    protected const HIDDEN = ['ViewPassword'];

    /**
     * @param  array<string, mixed>  $Uris  The related endpoints, by name (see uri()).
     * @param  array<string, mixed>  $extra  The fields SmugMug sent that the model has no property for.
     */
    public function __construct(
        public ?string $AccountStatus = null,
        public ?string $FirstName = null,
        public ?bool $FriendsView = null,
        public ?int $ImageCount = null,
        public ?bool $IsGettingStarted = null,
        public ?bool $IsTrial = null,
        public ?string $PendingClosureAction = null,
        public ?CarbonImmutable $PendingClosureDate = null,
        public ?bool $IsTrialExpired = null,
        public ?bool $IsTrialSitePrivate = null,
        public ?string $LastName = null,
        public ?string $NickName = null,
        public ?string $SortBy = null,
        public ?string $ViewPassHint = null,
        public ?string $ViewPassword = null,
        public ?string $Domain = null,
        public ?string $DomainOnly = null,
        public ?string $RefTag = null,
        public ?string $SiteID = null,
        public ?string $Name = null,
        public ?string $Plan = null,
        public ?string $TotalAccountSize = null,
        public ?string $TotalUploadedSize = null,
        public ?bool $QuickShare = null,
        public ?bool $IsAdmin = null,
        public ?string $City = null,
        public ?string $Country = null,
        public ?string $Email = null,
        public ?string $EmailSha256 = null,
        public ?string $PhoneNum = null,
        public ?string $State = null,
        public ?string $Street1 = null,
        public ?string $Street2 = null,
        public ?string $TimeZone = null,
        public ?string $Zip = null,
        public ?float $AccountSubscriptionDiscount = null,
        public ?bool $MfaEnabled = null,
        public ?bool $IsSubjectToVAT = null,
        public ?string $RetailerName = null,
        public ?string $Uri = null,
        public ?string $WebUri = null,
        public ?string $UriDescription = null,
        public ?string $ResponseLevel = null,
        public array $Uris = [],
        public array $extra = [],
    ) {}
}
