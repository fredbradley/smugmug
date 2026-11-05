<?php

namespace FredBradley\SmugMug\Data\Commerce;

use Carbon\CarbonImmutable;
use FredBradley\SmugMug\Data\Data;

/**
 * An order a buyer placed with the user, as the seller sees it: prices, profit and delivery.
 *
 * SmugMug sends it as: SalesOrder.
 */
readonly class SalesOrder extends Data
{
    /**
     * @param  array<string, mixed>  $Uris  The related endpoints, by name (see uri()).
     * @param  array<string, mixed>  $extra  The fields SmugMug sent that the model has no property for.
     */
    public function __construct(
        public ?int $OrderID = null,
        public ?CarbonImmutable $DatePlaced = null,
        public ?string $OrderStatus = null,
        public ?int $BoutiquePackaging = null,
        public mixed $BoutiquePackagingCost = null,
        public ?bool $PersonalDelivery = null,
        public ?string $BuyerEmail = null,
        public ?int $QualifiesForBoutiquePackaging = null,
        public ?bool $ProofDelayed = null,
        public ?int $ProofDelayReleaseDate = null,
        public ?bool $DelayedByPro = null,
        public ?bool $DelayReleasedByPro = null,
        public ?bool $ProDelayExpired = null,
        public ?int $ProDelayRemainingSecs = null,
        public ?bool $MultiProOrder = null,
        public ?bool $DownloadOnlyOrder = null,
        public ?int $TotalItemsSold = null,
        public mixed $SalePriceTotal = null,
        public mixed $BasePriceTotal = null,
        public mixed $PriceDifference = null,
        public ?string $CouponType = null,
        public ?string $CouponDescription = null,
        public mixed $CouponAmount = null,
        public mixed $FeesAndAdjustments = null,
        public mixed $GrossProfit = null,
        public mixed $NetProfit = null,
        public ?string $Uri = null,
        public ?string $WebUri = null,
        public ?string $UriDescription = null,
        public array $Uris = [],
        public array $extra = [],
    ) {}
}
