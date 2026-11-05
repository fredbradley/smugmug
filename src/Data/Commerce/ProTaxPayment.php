<?php

namespace FredBradley\SmugMug\Data\Commerce;

use FredBradley\SmugMug\Data\Data;

/**
 * The tax on a sale paid to the user.
 *
 * SmugMug sends it as: ProTaxPayments.
 */
readonly class ProTaxPayment extends Data
{
    /**
     * @param  array<string, mixed>|null  $ProTaxAmount
     * @param  array<string, mixed>  $Uris  The related endpoints, by name (see uri()).
     * @param  array<string, mixed>  $extra  The fields SmugMug sent that the model has no property for.
     */
    public function __construct(
        public ?string $ProTax = null,
        public ?array $ProTaxAmount = null,
        public array $Uris = [],
        public array $extra = [],
    ) {}
}
