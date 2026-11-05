<?php

namespace FredBradley\SmugMug\Data\Commerce;

use FredBradley\SmugMug\Data\Data;

/**
 * The image on a line of a sales order, and how it is cropped.
 *
 * SmugMug sends it as: SalesOrderItemContent.
 */
readonly class SalesOrderItemContent extends Data
{
    /**
     * @param  array<string, mixed>  $Uris  The related endpoints, by name (see uri()).
     * @param  array<string, mixed>  $extra  The fields SmugMug sent that the model has no property for.
     */
    public function __construct(
        public ?int $CropHeight = null,
        public ?int $CropLeft = null,
        public ?int $CropTop = null,
        public ?int $CropWidth = null,
        public ?int $CropRotation = null,
        public ?string $PrintMark = null,
        public ?bool $CropDisabled = null,
        public ?bool $CropAcknowledged = null,
        public ?string $ReplacePhotoUri = null,
        public mixed $AddOnBasePrice = null,
        public mixed $AddOnSalePrice = null,
        public ?string $Uri = null,
        public ?string $WebUri = null,
        public ?string $UriDescription = null,
        public array $Uris = [],
        public array $extra = [],
    ) {}
}
