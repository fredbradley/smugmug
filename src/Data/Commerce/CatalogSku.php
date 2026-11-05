<?php

namespace FredBradley\SmugMug\Data\Commerce;

use FredBradley\SmugMug\Data\Data;

/**
 * One saleable variant (SKU) of a product.
 *
 * SmugMug sends it as: CatalogSku.
 */
readonly class CatalogSku extends Data
{
    /**
     * @param  array<string, mixed>  $Uris  The related endpoints, by name (see uri()).
     * @param  array<string, mixed>  $extra  The fields SmugMug sent that the model has no property for.
     */
    public function __construct(
        public ?float $BorderHeight = null,
        public ?float $BorderWidth = null,
        public ?int $CartItemLimit = null,
        public ?bool $Enabled = null,
        public ?int $MinResHeight = null,
        public ?int $MinResWidth = null,
        public ?int $MaxQuantity = null,
        public ?bool $Priceable = null,
        public ?bool $RotationMatters = null,
        public ?int $SkuID = null,
        public ?string $Sku = null,
        public ?bool $canCrop = null,
        public ?string $SkuType = null,
        public ?string $SkuTypeCode = null,
        public ?string $MouldingWidth = null,
        public ?string $MouldingHeight = null,
        public ?string $FrameWidth = null,
        public ?string $FrameHeight = null,
        public ?bool $CanPrintmark = null,
        public ?string $AddonLabel = null,
        public ?string $Uri = null,
        public ?string $UriDescription = null,
        public array $Uris = [],
        public array $extra = [],
    ) {}
}
