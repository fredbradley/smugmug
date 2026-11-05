<?php

namespace FredBradley\SmugMug\Data\Commerce;

use FredBradley\SmugMug\Data\Data;

/**
 * The price of one product on a pricelist.
 *
 * SmugMug sends it as: PricelistSkuPrice.
 */
readonly class PricelistSkuPrice extends Data
{
    /**
     * @param  array<string, mixed>  $Uris  The related endpoints, by name (see uri()).
     * @param  array<string, mixed>  $extra  The fields SmugMug sent that the model has no property for.
     */
    public function __construct(
        public ?string $Currency = null,
        public ?float $Price = null,
        public ?string $BaseCurrency = null,
        public ?float $BasePrice = null,
        public ?string $Uri = null,
        public ?string $UriDescription = null,
        public ?string $ResponseLevel = null,
        public array $Uris = [],
        public array $extra = [],
    ) {}
}
