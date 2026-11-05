<?php

namespace FredBradley\SmugMug\Data\Album;

use FredBradley\SmugMug\Data\Data;

/**
 * A set of album settings to create albums from.
 *
 * SmugMug sends it as: AlbumTemplate.
 *
 * Deprecated by SmugMug, kept for older objects: External, Password, PasswordHint, Public.
 */
readonly class AlbumTemplate extends Data
{
    protected const HIDDEN = ['Password'];

    /**
     * @param  array<string, mixed>  $Uris  The related endpoints, by name (see uri()).
     * @param  array<string, mixed>  $extra  The fields SmugMug sent that the model has no property for.
     */
    public function __construct(
        public ?string $Name = null,
        public ?string $PrintmarkUri = null,
        public ?string $WatermarkUri = null,
        public ?string $TemplateUri = null,
        public ?bool $AllowDownloads = null,
        public ?string $Backprinting = null,
        public ?string $BoutiquePackaging = null,
        public ?bool $CanRank = null,
        public ?bool $Clean = null,
        public ?bool $Comments = null,
        public ?bool $EXIF = null,
        public ?bool $External = null,
        public ?bool $FamilyEdit = null,
        public ?bool $Filenames = null,
        public ?bool $FriendEdit = null,
        public ?bool $Geography = null,
        public ?string $Header = null,
        public ?bool $HideOwner = null,
        public ?string $InterceptShipping = null,
        public ?bool $IsDefault = null,
        public ?string $LargestSize = null,
        public ?string $MaxPhotoDownloadSize = null,
        public ?bool $PackagingBranding = null,
        public ?string $Password = null,
        public ?string $PasswordHint = null,
        public ?bool $Printable = null,
        public ?int $ProofDays = null,
        public ?bool $ProofDigital = null,
        public ?bool $Protected = null,
        public ?string $Privacy = null,
        public ?bool $Public = null,
        public ?bool $Share = null,
        public ?bool $ShowCoverImage = null,
        public ?bool $Slideshow = null,
        public ?string $SmugSearchable = null,
        public ?string $SortDirection = null,
        public ?string $SortMethod = null,
        public ?bool $SquareThumbs = null,
        public ?bool $Watermark = null,
        public ?bool $WorldSearchable = null,
        public ?bool $CommerceLightbox = null,
        public ?string $Uri = null,
        public ?string $UriDescription = null,
        public array $Uris = [],
        public array $extra = [],
    ) {}
}
