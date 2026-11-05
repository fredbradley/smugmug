<?php

namespace FredBradley\SmugMug\Data\Commerce;

use FredBradley\SmugMug\Data\Data;

/**
 * A product of the catalogue, such as a print size.
 *
 * SmugMug sends it as: CatalogProduct.
 */
readonly class CatalogProduct extends Data
{
    /**
     * @param  array<string, mixed>  $Uris  The related endpoints, by name (see uri()).
     * @param  array<string, mixed>  $extra  The fields SmugMug sent that the model has no property for.
     */
    public function __construct(
        public ?float $ColorCorrectionPrice = null,
        public ?string $Description = null,
        public ?bool $Enabled = null,
        public ?int $Group = null,
        public ?float $Height = null,
        public ?string $LockOrientation = null,
        public ?string $Name = null,
        public ?string $OptionType = null,
        public ?int $SortIndex = null,
        public ?string $ProductType = null,
        public ?float $Width = null,
        public ?string $Uri = null,
        public ?string $UriDescription = null,
        public array $Uris = [],
        public array $extra = [],
    ) {}
}
