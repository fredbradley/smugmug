<?php

namespace FredBradley\SmugMug\Data\Commerce;

use FredBradley\SmugMug\Data\Data;

/**
 * A category or subcategory of a vendor's products.
 *
 * SmugMug sends it as: CatalogCategory, CatalogSubCategory.
 */
readonly class CatalogCategory extends Data
{
    /**
     * @param  array<string, mixed>  $Uris  The related endpoints, by name (see uri()).
     * @param  array<string, mixed>  $extra  The fields SmugMug sent that the model has no property for.
     */
    public function __construct(
        public ?string $Description = null,
        public ?string $Name = null,
        public ?int $SortIndex = null,
        public ?string $Uri = null,
        public ?string $UriDescription = null,
        public array $Uris = [],
        public array $extra = [],
    ) {}
}
