<?php

namespace FredBradley\SmugMug\Data\Commerce;

use Carbon\CarbonImmutable;
use FredBradley\SmugMug\Data\Data;

/**
 * A pricelist: the products a user sells and their prices.
 *
 * SmugMug sends it as: PricelistKey, NodePricelist.
 */
readonly class Pricelist extends Data
{
    /**
     * @param  array<string, mixed>  $Uris  The related endpoints, by name (see uri()).
     * @param  array<string, mixed>  $extra  The fields SmugMug sent that the model has no property for.
     */
    public function __construct(
        public ?string $PricelistKey = null,
        public ?bool $AddonsAllowed = null,
        public ?bool $CardEnabled = null,
        public ?int $CardMarkupPercent = null,
        public ?bool $FoldedCardEnabled = null,
        public ?int $FoldedCardMarkupPercent = null,
        public ?string $Currency = null,
        public ?bool $ColorCorrection = null,
        public ?CarbonImmutable $DateCreated = null,
        public ?bool $Default = null,
        public ?string $Description = null,
        public ?CarbonImmutable $LastModified = null,
        public ?string $Name = null,
        public ?string $PrintVendor = null,
        public ?string $Scope = null,
        public ?string $Uri = null,
        public ?string $WebUri = null,
        public ?string $UriDescription = null,
        public ?string $ResponseLevel = null,
        public array $Uris = [],
        public array $extra = [],
    ) {}
}
