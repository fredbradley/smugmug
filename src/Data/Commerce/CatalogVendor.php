<?php

namespace FredBradley\SmugMug\Data\Commerce;

use FredBradley\SmugMug\Data\Data;

/**
 * A vendor of SmugMug's catalogue (SmugMug for digital products, or a print lab).
 *
 * SmugMug sends it as: CatalogVendor, CatalogPrintVendor.
 */
readonly class CatalogVendor extends Data
{
    /**
     * @param  array<string, mixed>  $Uris  The related endpoints, by name (see uri()).
     * @param  array<string, mixed>  $extra  The fields SmugMug sent that the model has no property for.
     */
    public function __construct(
        public ?string $Description = null,
        public ?string $Name = null,
        public ?string $Country = null,
        public ?int $SortIndex = null,
        public ?string $Uri = null,
        public ?string $UriDescription = null,
        public array $Uris = [],
        public array $extra = [],
    ) {}
}
