<?php

namespace FredBradley\SmugMug\Data\Commerce;

use FredBradley\SmugMug\Data\Data;

/**
 * A payment SmugMug made (or will make) to the user for their sales.
 *
 * SmugMug sends it as: ProPayments, ProSalePayments.
 */
readonly class ProPayment extends Data
{
    /**
     * @param  array<string, mixed>  $Uris  The related endpoints, by name (see uri()).
     * @param  array<string, mixed>  $extra  The fields SmugMug sent that the model has no property for.
     */
    public function __construct(
        public ?string $Created = null,
        public ?string $Updated = null,
        public ?string $Type = null,
        public ?string $Amount = null,
        public ?string $PayType = null,
        public ?string $Status = null,
        public ?string $BatchNotes = null,
        public array $Uris = [],
        public array $extra = [],
    ) {}
}
