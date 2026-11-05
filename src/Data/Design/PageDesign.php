<?php

namespace FredBradley\SmugMug\Data\Design;

use FredBradley\SmugMug\Data\Data;

/**
 * The design of a page: its background, lightbox, gallery style and layout.
 *
 * SmugMug sends it as: PageDesign, PublishedPageDesign, WorkingPageDesign, NodePageDesign, SiteDesignFolderDesign, SiteDesignGalleryDesign, SiteDesignGlobalDesign, SiteDesignHomepageDesign.
 */
readonly class PageDesign extends Data
{
    /**
     * @param  list<mixed>|null  $BackgroundSelectedImages
     * @param  array<string, mixed>|null  $LayoutRegionsBottom
     * @param  array<string, mixed>|null  $LayoutRegionsCenter
     * @param  array<string, mixed>|null  $LayoutRegionsLeft
     * @param  array<string, mixed>|null  $LayoutRegionsRight
     * @param  array<string, mixed>|null  $LayoutRegionsTop
     * @param  array<string, mixed>  $Uris  The related endpoints, by name (see uri()).
     * @param  array<string, mixed>  $extra  The fields SmugMug sent that the model has no property for.
     */
    public function __construct(
        public ?string $SkinMode = null,
        public ?bool $LightboxAutoHideControls = null,
        public ?string $LightboxBuyButtonSkin = null,
        public ?string $LightboxBuyButtonStyle = null,
        public ?bool $LightboxShowCaption = null,
        public ?bool $LightboxShowKeywords = null,
        public ?bool $LightboxShowTitle = null,
        public ?int $LightboxSlideshowSpeed = null,
        public ?string $LightboxSlideshowTransitionSpeed = null,
        public ?string $BackgroundType = null,
        public ?string $BackgroundImageSource = null,
        public ?string $BackgroundImageUri = null,
        public ?string $BackgroundAlbumUri = null,
        public ?array $BackgroundSelectedImages = null,
        public ?string $BackgroundKeywords = null,
        public ?bool $BackgroundLoop = null,
        public ?bool $BackgroundMask = null,
        public ?string $BackgroundMaskColor = null,
        public ?string $BackgroundMaskOpacity = null,
        public ?bool $BackgroundRandomImage = null,
        public ?string $BackgroundChangePhoto = null,
        public ?string $BackgroundSlideshowSource = null,
        public ?int $BackgroundSlideshowSpeed = null,
        public ?string $BackgroundSlideshowTransition = null,
        public ?int $BackgroundSlideshowTransitionSpeed = null,
        public ?int $BackgroundSplashDelay = null,
        public ?string $BackgroundSplashImageUri = null,
        public ?string $BackgroundFallbackImageUri = null,
        public ?string $BackgroundVerticalAlign = null,
        public ?string $GalleryColumnOrganicImageSize = null,
        public ?string $GalleryColumnOrganicInfoAlignment = null,
        public ?string $GalleryColumnOrganicInfoHover = null,
        public ?bool $GalleryColumnOrganicShowBreadcrumb = null,
        public ?string $GalleryColumnOrganicShowInfo = null,
        public ?int $GalleryColumnOrganicTileSpacing = null,
        public ?string $GalleryColumnOrganicBuyButtonDisplay = null,
        public ?string $GalleryColumnOrganicBuyButtonSize = null,
        public ?string $GalleryColumnOrganicBuyButtonSkin = null,
        public ?string $GalleryColumnOrganicBuyButtonStyle = null,
        public ?string $GalleryJournalBuyButtonSkin = null,
        public ?string $GalleryJournalImageCrop = null,
        public ?bool $GalleryJournalShowBreadcrumb = null,
        public ?string $GalleryRowOrganicImageSize = null,
        public ?string $GalleryRowOrganicInfoAlignment = null,
        public ?string $GalleryRowOrganicInfoHover = null,
        public ?bool $GalleryRowOrganicShowBreadcrumb = null,
        public ?string $GalleryRowOrganicShowInfo = null,
        public ?int $GalleryRowOrganicTileSpacing = null,
        public ?bool $GalleryRowOrganicVaryHeight = null,
        public ?string $GalleryRowOrganicBuyButtonDisplay = null,
        public ?string $GalleryRowOrganicBuyButtonSkin = null,
        public ?string $GalleryRowOrganicBuyButtonStyle = null,
        public ?string $GalleryRowOrganicBuyButtonSize = null,
        public ?bool $GallerySmugMugShowBreadcrumb = null,
        public ?string $GallerySmugMugThumbnailSize = null,
        public ?int $GallerySmugMugTileSpacing = null,
        public ?string $GalleryThumbnailsImageCrop = null,
        public ?string $GalleryThumbnailsImageSize = null,
        public ?string $GalleryThumbnailsInfoAlignment = null,
        public ?string $GalleryThumbnailsInfoHover = null,
        public ?bool $GalleryThumbnailsShowBreadcrumb = null,
        public ?string $GalleryThumbnailsShowInfo = null,
        public ?int $GalleryThumbnailsTileSpacing = null,
        public ?string $GalleryThumbnailsBuyButtonDisplay = null,
        public ?string $GalleryThumbnailsBuyButtonSize = null,
        public ?string $GalleryThumbnailsBuyButtonSkin = null,
        public ?string $GalleryThumbnailsBuyButtonStyle = null,
        public ?bool $LayoutFillHeight = null,
        public ?string $LayoutMargin = null,
        public ?string $LayoutPinnedRegion = null,
        public ?array $LayoutRegionsBottom = null,
        public ?array $LayoutRegionsCenter = null,
        public ?array $LayoutRegionsLeft = null,
        public ?array $LayoutRegionsRight = null,
        public ?array $LayoutRegionsTop = null,
        public ?int $LayoutSpacing = null,
        public ?int $LayoutWidth = null,
        public ?int $LayoutWidthType = null,
        public mixed $DateModified = null,
        public mixed $Status = null,
        public ?int $Version = null,
        public ?string $Uri = null,
        public ?string $UriDescription = null,
        public array $Uris = [],
        public array $extra = [],
    ) {}
}
