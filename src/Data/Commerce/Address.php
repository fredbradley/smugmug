<?php

namespace FredBradley\SmugMug\Data\Commerce;

use FredBradley\SmugMug\Data\Data;

/**
 * The billing or shipping address of an order.
 *
 * SmugMug sends it as: OrderBillingAddress, OrderShippingAddress, SalesOrderBillingAddress, SalesOrderShippingAddress.
 */
readonly class Address extends Data
{
    /**
     * @param  array<string, mixed>  $Uris  The related endpoints, by name (see uri()).
     * @param  array<string, mixed>  $extra  The fields SmugMug sent that the model has no property for.
     */
    public function __construct(
        public ?string $City = null,
        public ?string $Country = null,
        public ?string $FirstName = null,
        public ?string $LastName = null,
        public ?string $Method = null,
        public ?string $Phone = null,
        public ?string $State = null,
        public ?string $Street1 = null,
        public ?string $Street2 = null,
        public ?string $Zip = null,
        public ?string $Email = null,
        public ?string $PersonalDeliveryPro = null,
        public ?string $Uri = null,
        public ?string $UriDescription = null,
        public array $Uris = [],
        public array $extra = [],
    ) {}
}
