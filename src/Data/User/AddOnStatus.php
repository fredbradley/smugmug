<?php

namespace FredBradley\SmugMug\Data\User;

use Carbon\CarbonImmutable;
use FredBradley\SmugMug\Data\Data;

/**
 * The state of a paid add-on: RAW file management or collaborator seats.
 *
 * SmugMug sends it as: RawManagementAddOnStatus, CollaboratorAddOnStatus.
 */
readonly class AddOnStatus extends Data
{
    /**
     * @param  array<string, mixed>  $Uris  The related endpoints, by name (see uri()).
     * @param  array<string, mixed>  $extra  The fields SmugMug sent that the model has no property for.
     */
    public function __construct(
        public ?string $AddOnSubscriptionStatus = null,
        public ?CarbonImmutable $SubscriptionStartDate = null,
        public ?CarbonImmutable $CurrentBillingPeriodEndDate = null,
        public ?int $SubscriptionSeats = null,
        public ?bool $TaxInclusive = null,
        public mixed $CostPerTerm = null,
        public mixed $PastDueAmount = null,
        public ?int $IncludedSeats = null,
        public ?int $AllottedSeats = null,
        public ?int $SeatCapacity = null,
        public ?int $UtilizedSeats = null,
        public ?int $AvailableSeats = null,
        public ?int $PendingInvitations = null,
        public ?bool $CanUserAccessFiles = null,
        public ?bool $CanUserAddFiles = null,
        public ?int $BytesStored = null,
        public ?int $CurrentTierLowerBound = null,
        public ?int $CurrentTierUpperBound = null,
        public ?CarbonImmutable $ExpiredTrialContentRemovalDate = null,
        public ?string $Uri = null,
        public ?string $UriDescription = null,
        public array $Uris = [],
        public array $extra = [],
    ) {}
}
