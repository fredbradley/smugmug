<?php

namespace FredBradley\SmugMug\Data\Image;

use Carbon\CarbonImmutable;
use FredBradley\SmugMug\Data\Data;

/**
 * The EXIF, IPTC and XMP metadata of an image's file.
 *
 * SmugMug sends it as: ImageMetadata, AlbumImageMetadata.
 */
readonly class ImageMetadata extends Data
{
    /**
     * @param  array<string, mixed>  $Uris  The related endpoints, by name (see uri()).
     * @param  array<string, mixed>  $extra  The fields SmugMug sent that the model has no property for.
     */
    public function __construct(
        public ?CarbonImmutable $DateTimeCreated = null,
        public ?CarbonImmutable $DateTimeModified = null,
        public ?CarbonImmutable $DateTimeOriginal = null,
        public ?string $DateCreated = null,
        public ?CarbonImmutable $DateDigitized = null,
        public ?string $DateOriginal = null,
        public ?CarbonImmutable $MicroDateTimeCreated = null,
        public ?CarbonImmutable $MicroDateTimeDigitized = null,
        public ?string $TimeCreated = null,
        public ?string $TimeOriginal = null,
        public ?string $Title = null,
        public ?string $Caption = null,
        public ?string $UserComment = null,
        public ?string $Keywords = null,
        public ?string $Author = null,
        public ?string $Copyright = null,
        public ?string $CopyrightUrl = null,
        public ?string $CopyrightFlag = null,
        public ?string $UsageTerms = null,
        public ?string $Source = null,
        public ?string $Credit = null,
        public ?string $City = null,
        public ?string $State = null,
        public ?string $Country = null,
        public ?string $Rating = null,
        public ?string $CreatorContactInfo = null,
        public ?string $Category = null,
        public ?string $SupplementalCategories = null,
        public ?string $SpecialInstructions = null,
        public ?string $AuthorTitle = null,
        public ?string $CountryCode = null,
        public ?string $TransmissionReference = null,
        public ?string $Headline = null,
        public ?string $WriterEditor = null,
        public ?string $Lens = null,
        public ?string $Make = null,
        public ?string $Model = null,
        public ?string $Aperture = null,
        public ?string $Exposure = null,
        public ?int $ISO = null,
        public ?string $FocalLength = null,
        public ?string $FocalLength35mm = null,
        public ?string $CompressedBitsPerPixel = null,
        public ?string $Flash = null,
        public ?string $Metering = null,
        public ?string $ExposureProgram = null,
        public ?string $ExposureCompensation = null,
        public ?string $ExposureMode = null,
        public ?string $LightSource = null,
        public ?string $WhiteBalance = null,
        public ?string $DigitalZoomRatio = null,
        public ?string $Contrast = null,
        public ?string $Saturation = null,
        public ?string $Sharpness = null,
        public ?string $SubjectDistance = null,
        public ?string $SubjectRange = null,
        public ?string $SensingMethod = null,
        public ?string $ColorSpace = null,
        public ?string $Brightness = null,
        public ?string $LatitudeReference = null,
        public ?string $LongitudeReference = null,
        public mixed $Latitude = null,
        public mixed $Longitude = null,
        public ?string $AltitudeReference = null,
        public mixed $Altitude = null,
        public ?string $SceneCaptureType = null,
        public ?string $GainControl = null,
        public ?string $ScaleFactor = null,
        public ?string $CircleOfConfusion = null,
        public ?string $FieldOfView = null,
        public ?string $DepthOfField = null,
        public ?string $HyperfocalDistance = null,
        public ?string $NormalizedLightValue = null,
        public ?string $Duration = null,
        public ?string $AudioCodec = null,
        public ?string $VideoCodec = null,
        public ?string $Software = null,
        public ?string $SerialNumber = null,
        public ?string $LensSerialNumber = null,
        public ?string $Uri = null,
        public ?string $UriDescription = null,
        public array $Uris = [],
        public array $extra = [],
    ) {}
}
