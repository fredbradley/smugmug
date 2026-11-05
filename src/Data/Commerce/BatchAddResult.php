<?php

namespace FredBradley\SmugMug\Data\Commerce;

use FredBradley\SmugMug\Data\Data;

/**
 * The result of adding a SKU to the cart for many images: those that failed or were over the limit.
 *
 * SmugMug sends it as: CatalogSkuBatchAdd.
 */
readonly class BatchAddResult extends Data
{
    /**
     * @param  list<mixed>|null  $FailedImages
     * @param  list<mixed>|null  $ImagesOverTheCartItemLimit
     * @param  array<string, mixed>  $Uris  The related endpoints, by name (see uri()).
     * @param  array<string, mixed>  $extra  The fields SmugMug sent that the model has no property for.
     */
    public function __construct(
        public ?array $FailedImages = null,
        public ?array $ImagesOverTheCartItemLimit = null,
        public ?string $Uri = null,
        public ?string $UriDescription = null,
        public array $Uris = [],
        public array $extra = [],
    ) {}
}
