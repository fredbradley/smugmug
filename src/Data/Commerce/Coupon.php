<?php

namespace FredBradley\SmugMug\Data\Commerce;

use Carbon\CarbonImmutable;
use FredBradley\SmugMug\Data\Data;

/**
 * A coupon buyers can use in the user's shop.
 *
 * SmugMug sends it as: UserCoupon.
 *
 * Deprecated by SmugMug, kept for older objects: Albums.
 */
readonly class Coupon extends Data
{
    /**
     * @param  list<mixed>|null  $Albums
     * @param  list<mixed>|null  $Nodes
     * @param  list<mixed>|null  $CatalogRestrictions
     * @param  array<string, mixed>  $Uris  The related endpoints, by name (see uri()).
     * @param  array<string, mixed>  $extra  The fields SmugMug sent that the model has no property for.
     */
    public function __construct(
        public ?array $Albums = null,
        public ?array $Nodes = null,
        public ?array $CatalogRestrictions = null,
        public ?bool $Consumed = null,
        public ?string $CouponCode = null,
        public ?string $Currency = null,
        public ?string $Description = null,
        public ?float $DiscountAmount = null,
        public ?string $DiscountType = null,
        public ?bool $Expired = null,
        public ?bool $HasCatalogRestrictions = null,
        public ?bool $InternationalShipping = null,
        public ?float $MaxDiscount = null,
        public ?int $MaxItems = null,
        public ?int $MaxUses = null,
        public ?float $MinPurchase = null,
        public ?bool $Shipping = null,
        public ?bool $ShowBanner = null,
        public ?string $Title = null,
        public ?float $UsedAmount = null,
        public ?int $Uses = null,
        public ?CarbonImmutable $ValidFrom = null,
        public ?CarbonImmutable $ValidThru = null,
        public ?string $Uri = null,
        public ?string $UriDescription = null,
        public ?string $ResponseLevel = null,
        public array $Uris = [],
        public array $extra = [],
    ) {}
}
