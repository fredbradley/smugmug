<?php

namespace FredBradley\SmugMug\Data\Album;

use Carbon\CarbonImmutable;
use FredBradley\SmugMug\Data\Data;

/**
 * A gallery of images and videos, with its privacy, display, download and selling settings.
 *
 * SmugMug sends it as: Album.
 *
 * Deprecated by SmugMug, kept for older objects: NiceName, Title, External.
 */
readonly class Album extends Data
{
    protected const HIDDEN = ['DownloadPassword', 'Password'];

    /**
     * @param  array<string, mixed>  $Uris  The related endpoints, by name (see uri()).
     * @param  array<string, mixed>  $extra  The fields SmugMug sent that the model has no property for.
     */
    public function __construct(
        public ?string $NiceName = null,
        public ?string $UrlName = null,
        public ?string $Title = null,
        public ?string $Name = null,
        public ?string $PrintmarkUri = null,
        public ?string $WatermarkUri = null,
        public ?string $ThemeUri = null,
        public ?string $TemplateUri = null,
        public ?bool $AllowDownloads = null,
        public ?string $Backprinting = null,
        public ?string $BoutiquePackaging = null,
        public ?bool $CanRank = null,
        public ?bool $Clean = null,
        public ?bool $Comments = null,
        public ?string $Description = null,
        public ?string $DownloadPassword = null,
        public ?bool $EXIF = null,
        public ?bool $External = null,
        public ?bool $FamilyEdit = null,
        public ?bool $Filenames = null,
        public ?bool $FriendEdit = null,
        public ?bool $Geography = null,
        public ?string $Header = null,
        public ?bool $HideOwner = null,
        public ?string $InterceptShipping = null,
        public ?string $Keywords = null,
        public ?string $LargestSize = null,
        public ?bool $PackagingBranding = null,
        public ?string $Password = null,
        public ?string $PasswordHint = null,
        public ?bool $Printable = null,
        public ?string $Privacy = null,
        public ?int $ProofDays = null,
        public ?bool $ProofDigital = null,
        public ?bool $Protected = null,
        public ?bool $Share = null,
        public ?bool $Slideshow = null,
        public ?string $SmugSearchable = null,
        public ?string $SortDirection = null,
        public ?string $SortMethod = null,
        public ?bool $SquareThumbs = null,
        public ?string $UploadKey = null,
        public ?bool $Watermark = null,
        public ?bool $WorldSearchable = null,
        public ?string $SecurityType = null,
        public ?bool $CommerceLightbox = null,
        public ?string $MaxPhotoDownloadSize = null,
        public ?string $HighlightAlbumImageUri = null,
        public ?string $AlbumTemplateUri = null,
        public ?string $AlbumKey = null,
        public ?bool $CanBuy = null,
        public ?bool $CanFavorite = null,
        public ?string $FavoriteAlbumUrl = null,
        public ?CarbonImmutable $Date = null,
        public ?CarbonImmutable $LastUpdated = null,
        public ?CarbonImmutable $ImagesLastUpdated = null,
        public ?string $NodeID = null,
        public ?int $OriginalSizes = null,
        public ?int $TotalSizes = null,
        public ?int $ImageCount = null,
        public ?string $UrlPath = null,
        public ?bool $CanShare = null,
        public ?bool $HasDownloadPassword = null,
        public ?bool $Packages = null,
        public ?string $Uri = null,
        public ?string $WebUri = null,
        public ?string $UriDescription = null,
        public ?string $ResponseLevel = null,
        public array $Uris = [],
        public array $extra = [],
    ) {}
}
