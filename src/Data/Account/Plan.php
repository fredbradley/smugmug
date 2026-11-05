<?php

namespace FredBradley\SmugMug\Data\Account;

use FredBradley\SmugMug\Data\Data;

/**
 * A SmugMug plan the user can subscribe to, and its price.
 *
 * SmugMug sends it as: Plan.
 */
readonly class Plan extends Data
{
    /**
     * @param  list<mixed>|null  $ConsentTextWithSource
     * @param  list<mixed>|null  $ConsentTextWithoutSource
     * @param  array<string, mixed>  $Uris  The related endpoints, by name (see uri()).
     * @param  array<string, mixed>  $extra  The fields SmugMug sent that the model has no property for.
     */
    public function __construct(
        public ?string $PlanKey = null,
        public ?string $PlanLevel = null,
        public ?int $PlanLevelConst = null,
        public ?string $Description = null,
        public ?bool $Offered = null,
        public ?int $Term = null,
        public ?string $TermPer = null,
        public ?string $TermAdverb = null,
        public ?float $Amount = null,
        public ?string $Currency = null,
        public ?string $FormattedAmount = null,
        public mixed $DiscountedAmount = null,
        public ?float $DiscountPercentage = null,
        public ?string $DiscountCode = null,
        public ?bool $TaxIncluded = null,
        public ?array $ConsentTextWithSource = null,
        public ?string $ConsentTextWithSourceVersion = null,
        public ?array $ConsentTextWithoutSource = null,
        public ?string $ConsentTextWithoutSourceVersion = null,
        public ?string $Uri = null,
        public ?string $UriDescription = null,
        public array $Uris = [],
        public array $extra = [],
    ) {}
}
