<?php

namespace FredBradley\SmugMug\Data\Commerce;

use Carbon\CarbonImmutable;
use FredBradley\SmugMug\Data\Data;

/**
 * A shipment of an order: where it went, its carrier and tracking.
 *
 * SmugMug sends it as: OrderShipment, SalesOrderShipment.
 */
readonly class Shipment extends Data
{
    /**
     * @param  array<string, mixed>  $Uris  The related endpoints, by name (see uri()).
     * @param  array<string, mixed>  $extra  The fields SmugMug sent that the model has no property for.
     */
    public function __construct(
        public ?CarbonImmutable $DateReceived = null,
        public ?CarbonImmutable $DateShipped = null,
        public ?CarbonImmutable $DateSubmitted = null,
        public ?int $ProofDays = null,
        public ?string $Status = null,
        public ?string $TrackingNum = null,
        public ?string $City = null,
        public ?string $Country = null,
        public ?string $FirstName = null,
        public ?string $LastName = null,
        public ?string $Method = null,
        public ?string $Phone = null,
        public ?string $State = null,
        public ?string $Street1 = null,
        public ?string $Street2 = null,
        public ?string $Vendor = null,
        public ?string $Zip = null,
        public ?string $CarrierName = null,
        public ?string $TrackingUrl = null,
        public ?string $TrackingInfo = null,
        public ?string $StatusDescription = null,
        public ?string $StatusMessage = null,
        public ?bool $ProofDelay = null,
        public ?string $Uri = null,
        public ?string $UriDescription = null,
        public array $Uris = [],
        public array $extra = [],
    ) {}
}
