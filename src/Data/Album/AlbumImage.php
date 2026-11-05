<?php

namespace FredBradley\SmugMug\Data\Album;

use Carbon\CarbonImmutable;
use FredBradley\SmugMug\Data\Data;

/**
 * An image as it appears in one album: the image's fields with the album's key.
 *
 * SmugMug sends it as: AlbumImage.
 *
 * Deprecated by SmugMug, kept for older objects: Watermark, UploadKey, Date, Collectable.
 */
readonly class AlbumImage extends Data
{
    /**
     * @param  list<mixed>|null  $KeywordArray
     * @param  array<string, mixed>|null  $FormattedValues
     * @param  list<mixed>|null  $PublishedTo
     * @param  array<string, mixed>  $Uris  The related endpoints, by name (see uri()).
     * @param  array<string, mixed>  $extra  The fields SmugMug sent that the model has no property for.
     */
    public function __construct(
        public ?string $Title = null,
        public ?string $Caption = null,
        public ?string $Keywords = null,
        public ?array $KeywordArray = null,
        public ?string $Watermark = null,
        public ?float $Latitude = null,
        public ?float $Longitude = null,
        public ?int $Altitude = null,
        public ?bool $Hidden = null,
        public ?string $ThumbnailUrl = null,
        public ?string $FileName = null,
        public ?bool $Processing = null,
        public ?string $UploadKey = null,
        public ?CarbonImmutable $Date = null,
        public ?CarbonImmutable $DateTimeUploaded = null,
        public ?CarbonImmutable $DateTimeOriginal = null,
        public ?string $Format = null,
        public ?int $OriginalHeight = null,
        public ?int $OriginalWidth = null,
        public ?int $OriginalSize = null,
        public ?CarbonImmutable $LastUpdated = null,
        public ?bool $Collectable = null,
        public ?bool $IsArchive = null,
        public ?bool $IsVideo = null,
        public mixed $ComponentFileTypes = null,
        public ?bool $CanEdit = null,
        public ?bool $CanBuy = null,
        public ?bool $Protected = null,
        public ?bool $EZProject = null,
        public ?bool $Watermarked = null,
        public ?string $ImageKey = null,
        public ?int $Serial = null,
        public ?string $ArchivedUri = null,
        public ?int $ArchivedSize = null,
        public ?string $ArchivedMD5 = null,
        public ?string $Status = null,
        public ?string $SubStatus = null,
        public ?bool $CanShare = null,
        public ?bool $Comments = null,
        public ?bool $ShowKeywords = null,
        public ?array $FormattedValues = null,
        public ?string $PreferredDisplayFileExtension = null,
        public ?array $PublishedTo = null,
        public ?string $AlbumKey = null,
        public ?bool $Movable = null,
        public ?string $Origin = null,
        public ?string $Uri = null,
        public ?string $WebUri = null,
        public ?string $UriDescription = null,
        public array $Uris = [],
        public array $extra = [],
    ) {}
}
