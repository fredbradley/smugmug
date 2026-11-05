<?php

namespace FredBradley\SmugMug\Resources;

use FredBradley\SmugMug\Data\Album\Album;
use FredBradley\SmugMug\Data\Album\DownloadUrl;
use FredBradley\SmugMug\Data\Commerce\CatalogSkuPrice;
use FredBradley\SmugMug\Data\Data;
use FredBradley\SmugMug\Data\Image\ColorPreview;
use FredBradley\SmugMug\Data\Image\Image;
use FredBradley\SmugMug\Data\Image\ImageMetadata;
use FredBradley\SmugMug\Data\Image\ImageSize;
use FredBradley\SmugMug\Data\Image\ImageSizeDetails;
use FredBradley\SmugMug\Data\Image\ImageSizes;
use FredBradley\SmugMug\Data\Image\PointOfInterest;
use FredBradley\SmugMug\Data\Image\PointOfInterestCrops;
use FredBradley\SmugMug\Data\Image\PricelistExclusions;
use FredBradley\SmugMug\Data\Image\Region;
use FredBradley\SmugMug\Data\Social\Comment;
use FredBradley\SmugMug\Enums\Size;
use FredBradley\SmugMug\Listing;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\Image\AddImageKeywordsRequest;
use FredBradley\SmugMug\Requests\Image\ColorImageRequest;
use FredBradley\SmugMug\Requests\Image\ColorImagesRequest;
use FredBradley\SmugMug\Requests\Image\CopyImageRequest;
use FredBradley\SmugMug\Requests\Image\CreateImageCommentRequest;
use FredBradley\SmugMug\Requests\Image\CreateImageRegionRequest;
use FredBradley\SmugMug\Requests\Image\CropImageRequest;
use FredBradley\SmugMug\Requests\Image\CropImagesRequest;
use FredBradley\SmugMug\Requests\Image\DeleteImageRequest;
use FredBradley\SmugMug\Requests\Image\GetImageAlbumsRequest;
use FredBradley\SmugMug\Requests\Image\GetImageColorPreviewRequest;
use FredBradley\SmugMug\Requests\Image\GetImageCommentsRequest;
use FredBradley\SmugMug\Requests\Image\GetImageCustomSizeRequest;
use FredBradley\SmugMug\Requests\Image\GetImageDownloadRequest;
use FredBradley\SmugMug\Requests\Image\GetImageMetadataRequest;
use FredBradley\SmugMug\Requests\Image\GetImagePointOfInterestCropsRequest;
use FredBradley\SmugMug\Requests\Image\GetImagePointOfInterestRequest;
use FredBradley\SmugMug\Requests\Image\GetImagePricelistExclusionsRequest;
use FredBradley\SmugMug\Requests\Image\GetImagePricesRequest;
use FredBradley\SmugMug\Requests\Image\GetImageRegionsRequest;
use FredBradley\SmugMug\Requests\Image\GetImageRequest;
use FredBradley\SmugMug\Requests\Image\GetImageSizeDetailsRequest;
use FredBradley\SmugMug\Requests\Image\GetImageSizeRequest;
use FredBradley\SmugMug\Requests\Image\GetImageSizesRequest;
use FredBradley\SmugMug\Requests\Image\GetImagesRequest;
use FredBradley\SmugMug\Requests\Image\GetLargestImageRequest;
use FredBradley\SmugMug\Requests\Image\RemoveImageKeywordsRequest;
use FredBradley\SmugMug\Requests\Image\RemoveImagesWatermarkRequest;
use FredBradley\SmugMug\Requests\Image\RemoveImageWatermarkRequest;
use FredBradley\SmugMug\Requests\Image\RotateImageRequest;
use FredBradley\SmugMug\Requests\Image\RotateImagesRequest;
use FredBradley\SmugMug\Requests\Image\SearchImagesRequest;
use FredBradley\SmugMug\Requests\Image\UpdateImageRequest;
use FredBradley\SmugMug\Requests\Image\WatermarkImageRequest;
use FredBradley\SmugMug\Requests\Image\WatermarkImagesRequest;

/**
 * Images: their details, sizes, metadata and edits, one at a time or in bulk, from SmugMug::images().
 */
class ImageResource extends BaseResource
{
    /**
     * An image, by key (with its serial, as in jPPKD2c-1): GET /api/v2/image/{imageKey}.
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function get(string $imageKey, Options|array $options = []): ?Image
    {
        return $this->fetch(new GetImageRequest($imageKey, $this->options($options)));
    }

    /**
     * As get(), but null when SmugMug has no such object or the credentials may not see it.
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function find(string $imageKey, Options|array $options = []): ?Image
    {
        return $this->fetchOrNull(new GetImageRequest($imageKey, $this->options($options)));
    }

    /**
     * Several images at once (multi-get), by key: GET /api/v2/image/{imageKeys}.
     *
     * @param  list<string>|string  $imageKeys
     * @param  Options|array<string, mixed>  $options
     * @return Listing<Image>
     */
    public function many(string|array $imageKeys, Options|array $options = []): Listing
    {
        return $this->listing(fn (Options $o) => new GetImagesRequest($imageKeys, $o), $options);
    }

    /**
     * Change an image's details: PATCH /api/v2/image/{imageKey}.
     *
     *  - Title, Caption
     *  - Keywords: semicolon-separated, or KeywordArray: a list
     *  - Latitude, Longitude, Altitude: where it was taken
     *  - Hidden: hide it from visitors
     *
     * @param  array<string, mixed>|Data  $fields
     * @param  Options|array<string, mixed>  $options
     */
    public function update(string $imageKey, array|Data $fields, Options|array $options = []): ?Image
    {
        return $this->fetch(new UpdateImageRequest($imageKey, $fields, $this->options($options)));
    }

    /**
     * Delete an image from every album it is in: DELETE /api/v2/image/{imageKey}.
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function delete(string $imageKey, Options|array $options = []): bool
    {
        return $this->perform(new DeleteImageRequest($imageKey, $this->options($options)));
    }

    /**
     * Search images: GET /api/v2/image!search.
     *
     *  - Scope (required): the URI of the user, node or album to search in
     *  - Text: the text to search for
     *  - Keywords: only images with these keywords
     *  - SortMethod: DateTaken, DateUploaded or Popular
     *  - SortDirection: Ascending or Descending
     *  - Relevance: sort by relevance first
     *  - DateTakenStart, DateTakenEnd, DateUploadedStart, DateUploadedEnd: ISO 8601 dates
     *  - Type: Image or Video
     *
     * @param  Options|array<string, mixed>  $options
     * @return Listing<Image>
     */
    public function search(Options|array $options = []): Listing
    {
        return $this->listing(fn (Options $o) => new SearchImagesRequest($o), $options);
    }

    /**
     * The albums an image is in: GET /api/v2/image/{imageKey}!albums.
     *
     * @param  Options|array<string, mixed>  $options
     * @return Listing<Album>
     */
    public function albums(string $imageKey, Options|array $options = []): Listing
    {
        return $this->listing(fn (Options $o) => new GetImageAlbumsRequest($imageKey, $o), $options);
    }

    /**
     * The comments on an image: GET /api/v2/image/{imageKey}!comments.
     *
     * @param  Options|array<string, mixed>  $options
     * @return Listing<Comment>
     */
    public function comments(string $imageKey, Options|array $options = []): Listing
    {
        return $this->listing(fn (Options $o) => new GetImageCommentsRequest($imageKey, $o), $options);
    }

    /**
     * Comment on an image: POST /api/v2/image/{imageKey}!comments.
     *
     *  - Comment (required): the text
     *  - Name, Email, Link: who is commenting, for a comment without a token
     *  - Rating: a rating
     *  - SocialID, ServiceID: a social sign-in
     *
     * @param  array<string, mixed>|Data  $fields
     * @param  Options|array<string, mixed>  $options
     */
    public function addComment(string $imageKey, array|Data $fields, Options|array $options = []): ?Comment
    {
        return $this->fetch(new CreateImageCommentRequest($imageKey, $fields, $this->options($options)));
    }

    /**
     * Where to download an image's original: GET /api/v2/image/{imageKey}!download.
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function download(string $imageKey, Options|array $options = []): ?DownloadUrl
    {
        return $this->fetch(new GetImageDownloadRequest($imageKey, $this->options($options)));
    }

    /**
     * An image's EXIF, IPTC and XMP metadata: GET /api/v2/image/{imageKey}!metadata.
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function metadata(string $imageKey, Options|array $options = []): ?ImageMetadata
    {
        return $this->fetch(new GetImageMetadataRequest($imageKey, $this->options($options)));
    }

    /**
     * The pricelist products that may not be bought for an image: GET /api/v2/image/{imageKey}!pricelistexclusions.
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function pricelistExclusions(string $imageKey, Options|array $options = []): ?PricelistExclusions
    {
        return $this->fetch(new GetImagePricelistExclusionsRequest($imageKey, $this->options($options)));
    }

    /**
     * What an image sells for: GET /api/v2/image/{imageKey}!prices.
     *
     *  - Currency: USD, CAD, GBP, EUR, AUD, JPY, CHF, NZD, SEK or HKD
     *
     * @param  Options|array<string, mixed>  $options
     * @return Listing<CatalogSkuPrice>
     */
    public function prices(string $imageKey, Options|array $options = []): Listing
    {
        return $this->listing(fn (Options $o) => new GetImagePricesRequest($imageKey, $o), $options);
    }

    /**
     * The URL of every size of an image: GET /api/v2/image/{imageKey}!sizes.
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function sizes(string $imageKey, Options|array $options = []): ?ImageSizes
    {
        return $this->fetch(new GetImageSizesRequest($imageKey, $this->options($options)));
    }

    /**
     * Every size of an image in detail, with which may be used: GET /api/v2/image/{imageKey}!sizedetails.
     *
     *  - PrefetchSizes: the sizes to have SmugMug prepare now
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function sizeDetails(string $imageKey, Options|array $options = []): ?ImageSizeDetails
    {
        return $this->fetch(new GetImageSizeDetailsRequest($imageKey, $this->options($options)));
    }

    /**
     * The largest size of an image the credentials may see: GET /api/v2/image/{imageKey}!largestimage.
     *
     *  - MaxSize: the largest size to consider, such as ImageSizeX2Large
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function largest(string $imageKey, Options|array $options = []): ?ImageSize
    {
        return $this->fetch(new GetLargestImageRequest($imageKey, $this->options($options)));
    }

    /**
     * One size of an image: Tiny, Thumb, Small, Medium, Large, XLarge, X2Large to X5Large, 4K, 5K or Original: GET /api/v2/image/{imageKey}!size{size}.
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function size(string $imageKey, Size $size, Options|array $options = []): ?ImageSize
    {
        return $this->fetch(new GetImageSizeRequest($imageKey, $size, $this->options($options)));
    }

    /**
     * An image fitted to a size of your own: GET /api/v2/image/{imageKey}!sizecustom.
     *
     *  - width (required), height (required): the box to fit the image in, in pixels
     *  - fill: crop to fill the box
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function customSize(string $imageKey, Options|array $options = []): ?ImageSize
    {
        return $this->fetch(new GetImageCustomSizeRequest($imageKey, $this->options($options)));
    }

    /**
     * The point crops of an image centre on: GET /api/v2/image/{imageKey}!pointofinterest.
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function pointOfInterest(string $imageKey, Options|array $options = []): ?PointOfInterest
    {
        return $this->fetch(new GetImagePointOfInterestRequest($imageKey, $this->options($options)));
    }

    /**
     * The crops of an image around its point of interest: GET /api/v2/image/{imageKey}!poicrops.
     *
     * @param  Options|array<string, mixed>  $options
     * @return Listing<PointOfInterestCrops>
     */
    public function pointOfInterestCrops(string $imageKey, Options|array $options = []): Listing
    {
        return $this->listing(fn (Options $o) => new GetImagePointOfInterestCropsRequest($imageKey, $o), $options);
    }

    /**
     * The regions marked on an image: GET /api/v2/image/{imageKey}!regions.
     *
     * @param  Options|array<string, mixed>  $options
     * @return Listing<Region>
     */
    public function regions(string $imageKey, Options|array $options = []): Listing
    {
        return $this->listing(fn (Options $o) => new GetImageRegionsRequest($imageKey, $o), $options);
    }

    /**
     * Mark a region on an image: POST /api/v2/image/{imageKey}!regions.
     *
     *  - X (required), Y (required), Width (required), Height (required): the region, as fractions of the image
     *  - Type (required): Face, AF Point, Crop or Cover Image
     *
     * @param  array<string, mixed>|Data  $fields
     * @param  Options|array<string, mixed>  $options
     */
    public function addRegion(string $imageKey, array|Data $fields, Options|array $options = []): ?Region
    {
        return $this->fetch(new CreateImageRegionRequest($imageKey, $fields, $this->options($options)));
    }

    /**
     * Copy an image: POST /api/v2/image/{imageKey}!copy.
     *
     *  - AlbumUri: the album to copy the image into
     *
     * @param  array<string, mixed>  $parameters
     * @param  Options|array<string, mixed>  $options
     * @return array<string, mixed>
     */
    public function copy(string $imageKey, array $parameters = [], Options|array $options = []): array
    {
        return $this->fetch(new CopyImageRequest($imageKey, $parameters, $this->options($options)));
    }

    /**
     * An image's size with a colour effect applied: GET /api/v2/image/{imageKey}!color.
     *
     *  - Color: Auto, Tan, Sepia, B/W or Negative
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function colorPreview(string $imageKey, Options|array $options = []): ?ColorPreview
    {
        return $this->fetch(new GetImageColorPreviewRequest($imageKey, $this->options($options)));
    }

    /**
     * Apply a colour effect to an image: POST /api/v2/image/{imageKey}!color.
     *
     *  - Color (required): Auto, Tan, Sepia, B/W or Negative
     *
     * @param  array<string, mixed>  $parameters
     * @param  Options|array<string, mixed>  $options
     * @return array<string, mixed>
     */
    public function color(string $imageKey, array $parameters = [], Options|array $options = []): array
    {
        return $this->fetch(new ColorImageRequest($imageKey, $parameters, $this->options($options)));
    }

    /**
     * Crop an image: POST /api/v2/image/{imageKey}!crop.
     *
     *  - TopLeftX (required), TopLeftY (required), Width (required), Height (required): the crop, in pixels of the original
     *  - ThumbOnly (required): true to crop only the thumbnail
     *
     * @param  array<string, mixed>  $parameters
     * @param  Options|array<string, mixed>  $options
     * @return array<string, mixed>
     */
    public function crop(string $imageKey, array $parameters = [], Options|array $options = []): array
    {
        return $this->fetch(new CropImageRequest($imageKey, $parameters, $this->options($options)));
    }

    /**
     * Rotate or flip an image: POST /api/v2/image/{imageKey}!rotate.
     *
     *  - Degrees (required): 0, 90, 180 or 270
     *  - Flip (required): None, Horizontal or Vertical
     *
     * @param  array<string, mixed>  $parameters
     * @param  Options|array<string, mixed>  $options
     * @return array<string, mixed>
     */
    public function rotate(string $imageKey, array $parameters = [], Options|array $options = []): array
    {
        return $this->fetch(new RotateImageRequest($imageKey, $parameters, $this->options($options)));
    }

    /**
     * Watermark an image: POST /api/v2/image/{imageKey}!watermark.
     *
     *  - WatermarkUri: the watermark to apply
     *  - Mode: Add or Remove
     *
     * @param  array<string, mixed>  $parameters
     * @param  Options|array<string, mixed>  $options
     * @return array<string, mixed>
     */
    public function watermark(string $imageKey, array $parameters = [], Options|array $options = []): array
    {
        return $this->fetch(new WatermarkImageRequest($imageKey, $parameters, $this->options($options)));
    }

    /**
     * Remove an image's watermark: DELETE /api/v2/image/{imageKey}!watermark.
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function removeWatermark(string $imageKey, Options|array $options = []): bool
    {
        return $this->perform(new RemoveImageWatermarkRequest($imageKey, $this->options($options)));
    }

    /**
     * Rotate or flip several images: POST /api/v2/image!rotate.
     *
     *  - RotateUris (required): the images (a list of URIs)
     *  - Degrees (required): 0, 90, 180 or 270
     *  - Flip (required): None, Horizontal or Vertical
     *
     * @param  array<string, mixed>  $parameters
     * @param  Options|array<string, mixed>  $options
     * @return array<string, mixed>
     */
    public function rotateMany(array $parameters = [], Options|array $options = []): array
    {
        return $this->fetch(new RotateImagesRequest($parameters, $this->options($options)));
    }

    /**
     * Crop several images the same way: POST /api/v2/image!crop.
     *
     *  - CropUris (required): the images (a list of URIs)
     *  - TopLeftX (required), TopLeftY (required), Width (required), Height (required): the crop
     *  - ThumbOnly (required): true to crop only the thumbnails
     *
     * @param  array<string, mixed>  $parameters
     * @param  Options|array<string, mixed>  $options
     * @return array<string, mixed>
     */
    public function cropMany(array $parameters = [], Options|array $options = []): array
    {
        return $this->fetch(new CropImagesRequest($parameters, $this->options($options)));
    }

    /**
     * Apply a colour effect to several images: POST /api/v2/image!color.
     *
     *  - ColorUris (required): the images (a list of URIs)
     *  - Color (required): Auto, Tan, Sepia, B/W or Negative
     *
     * @param  array<string, mixed>  $parameters
     * @param  Options|array<string, mixed>  $options
     * @return array<string, mixed>
     */
    public function colorMany(array $parameters = [], Options|array $options = []): array
    {
        return $this->fetch(new ColorImagesRequest($parameters, $this->options($options)));
    }

    /**
     * Watermark several images: POST /api/v2/image!watermark.
     *
     *  - WatermarkUris (required): the images (a list of URIs)
     *  - WatermarkUri: the watermark to apply
     *  - Mode: Add or Remove
     *
     * @param  array<string, mixed>  $parameters
     * @param  Options|array<string, mixed>  $options
     * @return array<string, mixed>
     */
    public function watermarkMany(array $parameters = [], Options|array $options = []): array
    {
        return $this->fetch(new WatermarkImagesRequest($parameters, $this->options($options)));
    }

    /**
     * Remove the watermark from several images: DELETE /api/v2/image!watermark.
     *
     *  - WatermarkUris (required): the images (a list of URIs)
     *
     * @param  array<string, mixed>  $parameters
     * @param  Options|array<string, mixed>  $options
     * @return array<string, mixed>
     */
    public function removeWatermarkMany(array $parameters = [], Options|array $options = []): array
    {
        return $this->fetch(new RemoveImagesWatermarkRequest($parameters, $this->options($options)));
    }

    /**
     * Add keywords to several images: POST /api/v2/image!addkeywords.
     *
     *  - ImageUris (required): the images (a list of URIs)
     *  - Keywords (required): the keywords to add (semicolon-separated)
     *  - Async: true to do it in the background
     *
     * @param  array<string, mixed>  $parameters
     * @param  Options|array<string, mixed>  $options
     * @return array<string, mixed>
     */
    public function addKeywords(array $parameters = [], Options|array $options = []): array
    {
        return $this->fetch(new AddImageKeywordsRequest($parameters, $this->options($options)));
    }

    /**
     * Remove keywords from several images: POST /api/v2/image!removekeywords.
     *
     *  - ImageUris (required): the images (a list of URIs)
     *  - Keywords (required): the keywords to remove (semicolon-separated)
     *  - Async: true to do it in the background
     *
     * @param  array<string, mixed>  $parameters
     * @param  Options|array<string, mixed>  $options
     * @return array<string, mixed>
     */
    public function removeKeywords(array $parameters = [], Options|array $options = []): array
    {
        return $this->fetch(new RemoveImageKeywordsRequest($parameters, $this->options($options)));
    }
}
