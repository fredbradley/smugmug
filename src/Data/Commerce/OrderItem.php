<?php

namespace FredBradley\SmugMug\Data\Commerce;

use FredBradley\SmugMug\Data\Data;

/**
 * One line of an order.
 *
 * SmugMug sends it as: OrderItem.
 */
readonly class OrderItem extends Data
{
    /**
     * @param  array<string, mixed>  $Uris  The related endpoints, by name (see uri()).
     * @param  array<string, mixed>  $extra  The fields SmugMug sent that the model has no property for.
     */
    public function __construct(
        public ?string $Currency = null,
        public ?float $Price = null,
        public ?float $LinePrice = null,
        public ?int $Quantity = null,
        public ?string $LineIdentifier = null,
        public ?float $PrintLinePrice = null,
        public ?float $AddonLinePrice = null,
        public ?bool $ColorCorrection = null,
        public ?string $Uri = null,
        public ?string $UriDescription = null,
        public array $Uris = [],
        public array $extra = [],
    ) {}
}
