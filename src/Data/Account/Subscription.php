<?php

namespace FredBradley\SmugMug\Data\Account;

use Carbon\CarbonImmutable;
use FredBradley\SmugMug\Data\Data;

/**
 * One of the user's subscriptions to SmugMug.
 *
 * SmugMug sends it as: SubscriptionByKey.
 */
readonly class Subscription extends Data
{
    /**
     * @param  array<string, mixed>  $Uris  The related endpoints, by name (see uri()).
     * @param  array<string, mixed>  $extra  The fields SmugMug sent that the model has no property for.
     */
    public function __construct(
        public ?string $SubscriptionKey = null,
        public ?string $SubscriptionType = null,
        public ?string $PaymentProvider = null,
        public ?string $ProviderSubscriptionId = null,
        public ?bool $IsActive = null,
        public ?CarbonImmutable $SubscriptionStarted = null,
        public ?CarbonImmutable $SubscriptionEnded = null,
        public ?CarbonImmutable $CurrentPeriodStart = null,
        public ?CarbonImmutable $CurrentPeriodEnd = null,
        public mixed $PastDueAmount = null,
        public ?string $Uri = null,
        public ?string $UriDescription = null,
        public array $Uris = [],
        public array $extra = [],
    ) {}
}
