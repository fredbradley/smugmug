<?php

namespace FredBradley\SmugMug\Resources;

use FredBradley\SmugMug\Data\Album\AlbumImage;
use FredBradley\SmugMug\Data\Album\DownloadUrl;
use FredBradley\SmugMug\Data\Album\ShareUris;
use FredBradley\SmugMug\Data\Data;
use FredBradley\SmugMug\Data\Image\ImageMetadata;
use FredBradley\SmugMug\Data\Image\PricelistExclusions;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\AlbumImage\DeleteAlbumImageRequest;
use FredBradley\SmugMug\Requests\AlbumImage\GetAlbumImageDownloadRequest;
use FredBradley\SmugMug\Requests\AlbumImage\GetAlbumImageMetadataRequest;
use FredBradley\SmugMug\Requests\AlbumImage\GetAlbumImagePricelistExclusionsRequest;
use FredBradley\SmugMug\Requests\AlbumImage\GetAlbumImageRequest;
use FredBradley\SmugMug\Requests\AlbumImage\GetAlbumImageShareUrisRequest;
use FredBradley\SmugMug\Requests\AlbumImage\UpdateAlbumImageRequest;

/**
 * Images as they are in one album, from SmugMug::albumImages().
 */
class AlbumImageResource extends BaseResource
{
    /**
     * An image as it is in one album: GET /api/v2/album/{albumKey}/image/{imageKey}.
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function get(string $albumKey, string $imageKey, Options|array $options = []): ?AlbumImage
    {
        return $this->fetch(new GetAlbumImageRequest($albumKey, $imageKey, $this->options($options)));
    }

    /**
     * As get(), but null when SmugMug has no such object or the credentials may not see it.
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function find(string $albumKey, string $imageKey, Options|array $options = []): ?AlbumImage
    {
        return $this->fetchOrNull(new GetAlbumImageRequest($albumKey, $imageKey, $this->options($options)));
    }

    /**
     * Change an image's details: PATCH /api/v2/album/{albumKey}/image/{imageKey}.
     *
     *  - Title, Caption
     *  - Keywords: semicolon-separated, or KeywordArray: a list
     *  - Latitude, Longitude, Altitude: where it was taken
     *  - Hidden: hide it from visitors
     *
     * @param  array<string, mixed>|Data  $fields
     * @param  Options|array<string, mixed>  $options
     */
    public function update(string $albumKey, string $imageKey, array|Data $fields, Options|array $options = []): ?AlbumImage
    {
        return $this->fetch(new UpdateAlbumImageRequest($albumKey, $imageKey, $fields, $this->options($options)));
    }

    /**
     * Remove an image from an album (deleting it unless it is collected elsewhere): DELETE /api/v2/album/{albumKey}/image/{imageKey}.
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function delete(string $albumKey, string $imageKey, Options|array $options = []): bool
    {
        return $this->perform(new DeleteAlbumImageRequest($albumKey, $imageKey, $this->options($options)));
    }

    /**
     * Where to download an album image's original: GET /api/v2/album/{albumKey}/image/{imageKey}!download.
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function download(string $albumKey, string $imageKey, Options|array $options = []): ?DownloadUrl
    {
        return $this->fetch(new GetAlbumImageDownloadRequest($albumKey, $imageKey, $this->options($options)));
    }

    /**
     * An album image's EXIF, IPTC and XMP metadata: GET /api/v2/album/{albumKey}/image/{imageKey}!metadata.
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function metadata(string $albumKey, string $imageKey, Options|array $options = []): ?ImageMetadata
    {
        return $this->fetch(new GetAlbumImageMetadataRequest($albumKey, $imageKey, $this->options($options)));
    }

    /**
     * The pricelist products that may not be bought for an album image: GET /api/v2/album/{albumKey}/image/{imageKey}!pricelistexclusions.
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function pricelistExclusions(string $albumKey, string $imageKey, Options|array $options = []): ?PricelistExclusions
    {
        return $this->fetch(new GetAlbumImagePricelistExclusionsRequest($albumKey, $imageKey, $this->options($options)));
    }

    /**
     * The links to share an album image: GET /api/v2/album/{albumKey}/image/{imageKey}!shareuris.
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function shareUris(string $albumKey, string $imageKey, Options|array $options = []): ?ShareUris
    {
        return $this->fetch(new GetAlbumImageShareUrisRequest($albumKey, $imageKey, $this->options($options)));
    }
}
