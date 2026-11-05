<?php

namespace FredBradley\SmugMug\Resources;

use FredBradley\SmugMug\Data\Album\Album;
use FredBradley\SmugMug\Data\Album\AlbumImage;
use FredBradley\SmugMug\Data\Album\AppliedAlbumTemplate;
use FredBradley\SmugMug\Data\Album\Download;
use FredBradley\SmugMug\Data\Album\Package;
use FredBradley\SmugMug\Data\Album\ShareUris;
use FredBradley\SmugMug\Data\Commerce\CatalogSkuPrice;
use FredBradley\SmugMug\Data\Data;
use FredBradley\SmugMug\Data\Image\PricelistExclusions;
use FredBradley\SmugMug\Data\Social\Comment;
use FredBradley\SmugMug\Data\User\Grant;
use FredBradley\SmugMug\Listing;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\Album\AddAlbumSamplePhotosRequest;
use FredBradley\SmugMug\Requests\Album\ApplyAlbumTemplateRequest;
use FredBradley\SmugMug\Requests\Album\CollectAlbumImagesRequest;
use FredBradley\SmugMug\Requests\Album\CreateAlbumCommentRequest;
use FredBradley\SmugMug\Requests\Album\CreateAlbumDownloadRequest;
use FredBradley\SmugMug\Requests\Album\CreateAlbumGrantRequest;
use FredBradley\SmugMug\Requests\Album\DeleteAlbumDownloadRequest;
use FredBradley\SmugMug\Requests\Album\DeleteAlbumImagesRequest;
use FredBradley\SmugMug\Requests\Album\DeleteAlbumRequest;
use FredBradley\SmugMug\Requests\Album\GetAlbumCommentsRequest;
use FredBradley\SmugMug\Requests\Album\GetAlbumDownloadsRequest;
use FredBradley\SmugMug\Requests\Album\GetAlbumGeoMediaRequest;
use FredBradley\SmugMug\Requests\Album\GetAlbumGrantsRequest;
use FredBradley\SmugMug\Requests\Album\GetAlbumHighlightImageRequest;
use FredBradley\SmugMug\Requests\Album\GetAlbumImagesRequest;
use FredBradley\SmugMug\Requests\Album\GetAlbumPackagesRequest;
use FredBradley\SmugMug\Requests\Album\GetAlbumPopularMediaRequest;
use FredBradley\SmugMug\Requests\Album\GetAlbumPricelistExclusionsRequest;
use FredBradley\SmugMug\Requests\Album\GetAlbumPricesRequest;
use FredBradley\SmugMug\Requests\Album\GetAlbumRequest;
use FredBradley\SmugMug\Requests\Album\GetAlbumShareUrisRequest;
use FredBradley\SmugMug\Requests\Album\GetAlbumsRequest;
use FredBradley\SmugMug\Requests\Album\MoveAlbumImagesRequest;
use FredBradley\SmugMug\Requests\Album\SearchAlbumsRequest;
use FredBradley\SmugMug\Requests\Album\SortAlbumImagesRequest;
use FredBradley\SmugMug\Requests\Album\UpdateAlbumDownloadRequest;
use FredBradley\SmugMug\Requests\Album\UpdateAlbumRequest;
use FredBradley\SmugMug\Requests\Album\UploadAlbumImageFromExternalResourceRequest;
use FredBradley\SmugMug\Requests\Album\UploadAlbumImageFromUriRequest;

/**
 * Albums: their settings, images, comments, downloads and bulk image operations, from SmugMug::albums().
 */
class AlbumResource extends BaseResource
{
    /**
     * An album, by key: GET /api/v2/album/{albumKey}.
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function get(string $albumKey, Options|array $options = []): ?Album
    {
        return $this->fetch(new GetAlbumRequest($albumKey, $this->options($options)));
    }

    /**
     * As get(), but null when SmugMug has no such object or the credentials may not see it.
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function find(string $albumKey, Options|array $options = []): ?Album
    {
        return $this->fetchOrNull(new GetAlbumRequest($albumKey, $this->options($options)));
    }

    /**
     * Several albums at once (multi-get), by key: GET /api/v2/album/{albumKeys}.
     *
     * @param  list<string>|string  $albumKeys
     * @param  Options|array<string, mixed>  $options
     * @return Listing<Album>
     */
    public function many(string|array $albumKeys, Options|array $options = []): Listing
    {
        return $this->listing(fn (Options $o) => new GetAlbumsRequest($albumKeys, $o), $options);
    }

    /**
     * Change an album's settings: PATCH /api/v2/album/{albumKey}.
     *
     *  - Name, UrlName, Description, Keywords: what the album is called and says
     *  - Privacy: Public, Unlisted or Private
     *  - SecurityType: None, Password or GrantAccess
     *  - Password, PasswordHint
     *  - SmugSearchable: No or Inherit from User
     *  - WorldSearchable
     *  - SortMethod: Position, Caption, Filename, Date Uploaded, Date Modified or Date Taken
     *  - SortDirection: Ascending or Descending
     *  - AllowDownloads, MaxPhotoDownloadSize, DownloadPassword: downloads
     *  - LargestSize: the largest size visitors may see (Medium to Original)
     *  - Watermark, WatermarkUri: watermarking
     *  - Printable, PrintmarkUri, Backprinting, BoutiquePackaging, InterceptShipping, PackagingBranding, ProofDays, ProofDigital, CommerceLightbox: selling
     *  - Comments, CanRank, Share, Slideshow, Clean, EXIF, Filenames, Geography, HideOwner, Protected, SquareThumbs, FamilyEdit, FriendEdit: display and interaction
     *  - Header: Custom or SmugMug
     *  - ThemeUri, TemplateUri, AlbumTemplateUri, HighlightAlbumImageUri
     *  - UploadKey: the guest upload key
     *  - AutoRename: rename instead of failing when the UrlName is taken
     *
     * @param  array<string, mixed>|Data  $fields
     * @param  Options|array<string, mixed>  $options
     */
    public function update(string $albumKey, array|Data $fields, Options|array $options = []): ?Album
    {
        return $this->fetch(new UpdateAlbumRequest($albumKey, $fields, $this->options($options)));
    }

    /**
     * Delete an album and its images (to the trash): DELETE /api/v2/album/{albumKey}.
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function delete(string $albumKey, Options|array $options = []): bool
    {
        return $this->perform(new DeleteAlbumRequest($albumKey, $this->options($options)));
    }

    /**
     * Search albums: GET /api/v2/album!search.
     *
     *  - Scope (required): the URI of the user or node to search in
     *  - Text: the text to search for
     *  - SortMethod: Rank or LastUpdated
     *  - SortDirection: Ascending or Descending
     *
     * @param  Options|array<string, mixed>  $options
     * @return Listing<Album>
     */
    public function search(Options|array $options = []): Listing
    {
        return $this->listing(fn (Options $o) => new SearchAlbumsRequest($o), $options);
    }

    /**
     * The images in an album: GET /api/v2/album/{albumKey}!images.
     *
     *  - FileNames: only these file names (comma-separated)
     *  - SkuId: only the images that may be sold as this SKU
     *
     * @param  Options|array<string, mixed>  $options
     * @return Listing<AlbumImage>
     */
    public function images(string $albumKey, Options|array $options = []): Listing
    {
        return $this->listing(fn (Options $o) => new GetAlbumImagesRequest($albumKey, $o), $options);
    }

    /**
     * An album's most viewed images: GET /api/v2/album/{albumKey}!popularmedia.
     *
     *  - MediaType: All, Images or Videos
     *
     * @param  Options|array<string, mixed>  $options
     * @return Listing<AlbumImage>
     */
    public function popularMedia(string $albumKey, Options|array $options = []): Listing
    {
        return $this->listing(fn (Options $o) => new GetAlbumPopularMediaRequest($albumKey, $o), $options);
    }

    /**
     * An album's images that carry a location: GET /api/v2/album/{albumKey}!geomedia.
     *
     *  - SortMethod: Popular, Oldest or Newest
     *  - MediaType: All, Images or Videos
     *
     * @param  Options|array<string, mixed>  $options
     * @return Listing<AlbumImage>
     */
    public function geoMedia(string $albumKey, Options|array $options = []): Listing
    {
        return $this->listing(fn (Options $o) => new GetAlbumGeoMediaRequest($albumKey, $o), $options);
    }

    /**
     * The image that represents an album: GET /api/v2/album/{albumKey}!highlightimage.
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function highlightImage(string $albumKey, Options|array $options = []): ?AlbumImage
    {
        return $this->fetch(new GetAlbumHighlightImageRequest($albumKey, $this->options($options)));
    }

    /**
     * The links to share an album: GET /api/v2/album/{albumKey}!shareuris.
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function shareUris(string $albumKey, Options|array $options = []): ?ShareUris
    {
        return $this->fetch(new GetAlbumShareUrisRequest($albumKey, $this->options($options)));
    }

    /**
     * The comments on an album: GET /api/v2/album/{albumKey}!comments.
     *
     * @param  Options|array<string, mixed>  $options
     * @return Listing<Comment>
     */
    public function comments(string $albumKey, Options|array $options = []): Listing
    {
        return $this->listing(fn (Options $o) => new GetAlbumCommentsRequest($albumKey, $o), $options);
    }

    /**
     * Comment on an album: POST /api/v2/album/{albumKey}!comments.
     *
     *  - Comment (required): the text
     *  - Name, Email, Link: who is commenting, for a comment without a token
     *  - Rating: a rating
     *  - SocialID, ServiceID: a social sign-in
     *
     * @param  array<string, mixed>|Data  $fields
     * @param  Options|array<string, mixed>  $options
     */
    public function addComment(string $albumKey, array|Data $fields, Options|array $options = []): ?Comment
    {
        return $this->fetch(new CreateAlbumCommentRequest($albumKey, $fields, $this->options($options)));
    }

    /**
     * The downloads of an album's originals: GET /api/v2/album/{albumKey}!download.
     *
     * @param  Options|array<string, mixed>  $options
     * @return Listing<Download>
     */
    public function downloads(string $albumKey, Options|array $options = []): Listing
    {
        return $this->listing(fn (Options $o) => new GetAlbumDownloadsRequest($albumKey, $o), $options);
    }

    /**
     * Prepare a download (zip) of an album's originals: POST /api/v2/album/{albumKey}!download.
     *
     * @param  array<string, mixed>  $parameters
     * @param  Options|array<string, mixed>  $options
     * @return array<string, mixed>
     */
    public function createDownload(string $albumKey, array $parameters = [], Options|array $options = []): array
    {
        return $this->fetch(new CreateAlbumDownloadRequest($albumKey, $parameters, $this->options($options)));
    }

    /**
     * Prepare the album's download again: PUT /api/v2/album/{albumKey}!download.
     *
     * @param  array<string, mixed>  $parameters
     * @param  Options|array<string, mixed>  $options
     * @return array<string, mixed>
     */
    public function updateDownload(string $albumKey, array $parameters = [], Options|array $options = []): array
    {
        return $this->fetch(new UpdateAlbumDownloadRequest($albumKey, $parameters, $this->options($options)));
    }

    /**
     * Delete an album's prepared downloads: DELETE /api/v2/album/{albumKey}!download.
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function deleteDownload(string $albumKey, Options|array $options = []): bool
    {
        return $this->perform(new DeleteAlbumDownloadRequest($albumKey, $this->options($options)));
    }

    /**
     * What an album's images sell for: GET /api/v2/album/{albumKey}!prices.
     *
     *  - Currency: USD, CAD, GBP, EUR, AUD, JPY, CHF, NZD, SEK or HKD
     *
     * @param  Options|array<string, mixed>  $options
     * @return Listing<CatalogSkuPrice>
     */
    public function prices(string $albumKey, Options|array $options = []): Listing
    {
        return $this->listing(fn (Options $o) => new GetAlbumPricesRequest($albumKey, $o), $options);
    }

    /**
     * The pricelist products that may not be bought from an album: GET /api/v2/album/{albumKey}!pricelistexclusions.
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function pricelistExclusions(string $albumKey, Options|array $options = []): ?PricelistExclusions
    {
        return $this->fetch(new GetAlbumPricelistExclusionsRequest($albumKey, $this->options($options)));
    }

    /**
     * Who has been granted access to an album: GET /api/v2/album/{albumKey}!grants.
     *
     * @param  Options|array<string, mixed>  $options
     * @return Listing<Grant>
     */
    public function grants(string $albumKey, Options|array $options = []): Listing
    {
        return $this->listing(fn (Options $o) => new GetAlbumGrantsRequest($albumKey, $o), $options);
    }

    /**
     * Grant someone access to an album (with SecurityType GrantAccess): POST /api/v2/album/{albumKey}!grants.
     *
     *  - ContactUri: the contact to grant access to
     *  - Email: or an email address
     *
     * @param  array<string, mixed>|Data  $fields
     * @param  Options|array<string, mixed>  $options
     */
    public function grant(string $albumKey, array|Data $fields, Options|array $options = []): ?Grant
    {
        return $this->fetch(new CreateAlbumGrantRequest($albumKey, $fields, $this->options($options)));
    }

    /**
     * The packages sold from an album: GET /api/v2/album/{albumKey}!packages.
     *
     * @param  Options|array<string, mixed>  $options
     * @return Listing<Package>
     */
    public function packages(string $albumKey, Options|array $options = []): Listing
    {
        return $this->listing(fn (Options $o) => new GetAlbumPackagesRequest($albumKey, $o), $options);
    }

    /**
     * Apply an album template's settings to an album: POST /api/v2/album/{albumKey}!applyalbumtemplate.
     *
     *  - AlbumTemplateUri (required): the album template to apply
     *
     * @param  array<string, mixed>|Data  $fields
     * @param  Options|array<string, mixed>  $options
     */
    public function applyTemplate(string $albumKey, array|Data $fields, Options|array $options = []): ?AppliedAlbumTemplate
    {
        return $this->fetch(new ApplyAlbumTemplateRequest($albumKey, $fields, $this->options($options)));
    }

    /**
     * Collect images from other albums into an album (they stay where they are too): POST /api/v2/album/{albumKey}!collectimages.
     *
     *  - CollectUris (required): the images to collect (a list of image URIs)
     *  - Async: true to do it in the background
     *
     * @param  array<string, mixed>  $parameters
     * @param  Options|array<string, mixed>  $options
     * @return array<string, mixed>
     */
    public function collectImages(string $albumKey, array $parameters = [], Options|array $options = []): array
    {
        return $this->fetch(new CollectAlbumImagesRequest($albumKey, $parameters, $this->options($options)));
    }

    /**
     * Move images into an album from other albums: POST /api/v2/album/{albumKey}!moveimages.
     *
     *  - MoveUris (required): the album images to move here (a list of album image URIs)
     *  - Async: true to do it in the background
     *
     * @param  array<string, mixed>  $parameters
     * @param  Options|array<string, mixed>  $options
     * @return array<string, mixed>
     */
    public function moveImages(string $albumKey, array $parameters = [], Options|array $options = []): array
    {
        return $this->fetch(new MoveAlbumImagesRequest($albumKey, $parameters, $this->options($options)));
    }

    /**
     * Reorder an album's images (for SortMethod Position): POST /api/v2/album/{albumKey}!sortimages.
     *
     *  - MoveUris (required): the album images to move (a list of URIs)
     *  - MoveLocation (required): Before or After
     *  - Uri (required): the album image to move them before or after
     *
     * @param  array<string, mixed>  $parameters
     * @param  Options|array<string, mixed>  $options
     * @return array<string, mixed>
     */
    public function sortImages(string $albumKey, array $parameters = [], Options|array $options = []): array
    {
        return $this->fetch(new SortAlbumImagesRequest($albumKey, $parameters, $this->options($options)));
    }

    /**
     * Delete several images from an album at once: POST /api/v2/album/{albumKey}!deleteimages.
     *
     *  - DeleteUris (required): the album images to delete (a list of URIs)
     *  - Async: true to do it in the background
     *
     * @param  array<string, mixed>  $parameters
     * @param  Options|array<string, mixed>  $options
     * @return array<string, mixed>
     */
    public function deleteImages(string $albumKey, array $parameters = [], Options|array $options = []): array
    {
        return $this->fetch(new DeleteAlbumImagesRequest($albumKey, $parameters, $this->options($options)));
    }

    /**
     * Fill an album with SmugMug's sample photos: POST /api/v2/album/{albumKey}!addsamplephotos.
     *
     * @param  array<string, mixed>  $parameters
     * @param  Options|array<string, mixed>  $options
     * @return array<string, mixed>
     */
    public function addSamplePhotos(string $albumKey, array $parameters = [], Options|array $options = []): array
    {
        return $this->fetch(new AddAlbumSamplePhotosRequest($albumKey, $parameters, $this->options($options)));
    }

    /**
     * Have SmugMug fetch a file from an address into an album: POST /api/v2/album/{albumKey}!uploadfromuri.
     *
     *  - Uri (required): the address SmugMug fetches the file from
     *  - FileName, Title, Caption, Keywords, Hidden: the new image's details
     *  - ByteCount, MD5Sum: to check the file
     *  - Cookie: a cookie to send with the fetch
     *  - AllowInsecure: allow an http address
     *
     * @param  array<string, mixed>  $parameters
     * @param  Options|array<string, mixed>  $options
     * @return array<string, mixed>
     */
    public function uploadFromUri(string $albumKey, array $parameters = [], Options|array $options = []): array
    {
        return $this->fetch(new UploadAlbumImageFromUriRequest($albumKey, $parameters, $this->options($options)));
    }

    /**
     * Import a file from a connected service into an album: POST /api/v2/album/{albumKey}!uploadfromexternalresource.
     *
     *  - Uri (required): the external resource
     *  - Id: its id
     *
     * @param  array<string, mixed>  $parameters
     * @param  Options|array<string, mixed>  $options
     * @return array<string, mixed>
     */
    public function uploadFromExternalResource(string $albumKey, array $parameters = [], Options|array $options = []): array
    {
        return $this->fetch(new UploadAlbumImageFromExternalResourceRequest($albumKey, $parameters, $this->options($options)));
    }
}
