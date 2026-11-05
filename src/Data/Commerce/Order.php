<?php

namespace FredBradley\SmugMug\Data\Commerce;

use Carbon\CarbonImmutable;
use FredBradley\SmugMug\Data\Data;

/**
 * An order the user placed as a buyer.
 *
 * SmugMug sends it as: Order.
 */
readonly class Order extends Data
{
    /**
     * @param  array<string, mixed>  $Uris  The related endpoints, by name (see uri()).
     * @param  array<string, mixed>  $extra  The fields SmugMug sent that the model has no property for.
     */
    public function __construct(
        public ?int $OrderID = null,
        public ?CarbonImmutable $DatePlaced = null,
        public ?string $Status = null,
        public ?string $ThirdPartyRedirectUrl = null,
        public ?string $StatusMessage = null,
        public ?string $ShipCity = null,
        public ?string $ShipState = null,
        public ?string $ShipCountry = null,
        public ?string $OrderAmount = null,
        public ?string $BuyerCurrency = null,
        public ?float $CouponTotal = null,
        public ?string $CouponType = null,
        public ?float $GrandTotal = null,
        public ?int $ItemCount = null,
        public ?float $ItemTotal = null,
        public ?float $ProcessingFee = null,
        public ?float $ShippingTotal = null,
        public ?float $TaxTotal = null,
        public ?string $TaxLabel = null,
        public ?bool $HasVAT = null,
        public ?bool $IsPayPalFlow = null,
        public ?string $BackToShopUrl = null,
        public ?bool $BackToLibrary = null,
        public ?string $Uri = null,
        public ?string $WebUri = null,
        public ?string $UriDescription = null,
        public array $Uris = [],
        public array $extra = [],
    ) {}
}
