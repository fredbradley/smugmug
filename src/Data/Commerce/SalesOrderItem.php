<?php

namespace FredBradley\SmugMug\Data\Commerce;

use FredBradley\SmugMug\Data\Data;

/**
 * One line of a sales order.
 *
 * SmugMug sends it as: SalesOrderItem.
 */
readonly class SalesOrderItem extends Data
{
    /**
     * @param  array<string, mixed>  $Uris  The related endpoints, by name (see uri()).
     * @param  array<string, mixed>  $extra  The fields SmugMug sent that the model has no property for.
     */
    public function __construct(
        public mixed $BasePrice = null,
        public mixed $Price = null,
        public mixed $LinePrice = null,
        public ?int $Quantity = null,
        public ?string $LineIdentifier = null,
        public mixed $PrintLinePrice = null,
        public mixed $AddonLinePrice = null,
        public ?bool $ColorCorrection = null,
        public ?bool $CanColorCorrect = null,
        public ?string $Uri = null,
        public ?string $UriDescription = null,
        public array $Uris = [],
        public array $extra = [],
    ) {}
}
