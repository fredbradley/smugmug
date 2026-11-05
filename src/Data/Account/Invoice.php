<?php

namespace FredBradley\SmugMug\Data\Account;

use Carbon\CarbonImmutable;
use FredBradley\SmugMug\Data\Data;

/**
 * An invoice for a subscription.
 *
 * SmugMug sends it as: InvoiceByKey.
 */
readonly class Invoice extends Data
{
    /**
     * @param  array<string, mixed>  $Uris  The related endpoints, by name (see uri()).
     * @param  array<string, mixed>  $extra  The fields SmugMug sent that the model has no property for.
     */
    public function __construct(
        public ?string $InvoiceKey = null,
        public ?string $Status = null,
        public ?CarbonImmutable $InvoiceStartDate = null,
        public ?CarbonImmutable $InvoiceEndDate = null,
        public mixed $TotalAmount = null,
        public mixed $TaxTotalAmount = null,
        public mixed $SubTotalAmount = null,
        public ?string $Description = null,
        public ?string $Uri = null,
        public ?string $WebUri = null,
        public ?string $UriDescription = null,
        public array $Uris = [],
        public array $extra = [],
    ) {}
}
