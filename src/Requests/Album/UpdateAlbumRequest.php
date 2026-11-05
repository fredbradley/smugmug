<?php

namespace FredBradley\SmugMug\Requests\Album;

use FredBradley\SmugMug\Data\Album\Album;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\WriteRequest;
use FredBradley\SmugMug\Support\Path;
use JsonSerializable;
use Saloon\Enums\Method;

/**
 * Change an album's settings: PATCH /api/v2/album/{albumKey}.
 *
 * Parameters (in the body):
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
 */
class UpdateAlbumRequest extends WriteRequest
{
    protected Method $method = Method::PATCH;

    protected ?string $dto = Album::class;

    /**
     * @param  array<mixed>|JsonSerializable  $body  SmugMug's field names and values, or a model.
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $albumKey, array|JsonSerializable $body = [], Options|array $options = [])
    {
        $this->setBody($body, $options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/album/'.Path::segment($this->albumKey);
    }
}
