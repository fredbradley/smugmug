<?php

namespace FredBradley\SmugMug\Resources;

use FredBradley\SmugMug\Data\Upload\UploadResult;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\Upload\CreateAlbumImageRequest;
use FredBradley\SmugMug\Requests\Upload\ReplaceImageMediaRequest;
use FredBradley\SmugMug\Requests\Upload\UploadImageRequest;
use FredBradley\SmugMug\Upload\Media;

/**
 * Uploads, from SmugMug::uploads(). Every upload needs an access token (SmugMug::withToken()) with Add
 * permission, or Modify to replace an image.
 *
 *     SmugMug::withToken($token, $secret)->uploads()->upload('SJT3DX', Media::fromPath('/photos/beach.jpg'), [
 *         'Title' => 'Beach',
 *         'Keywords' => ['summer', 'sea'],
 *     ]);
 */
class UploadResource extends BaseResource
{
    /**
     * Upload a file into an album with SmugMug's uploader: POST https://upload.smugmug.com/.
     *
     * @param  string  $album  The album's key or URI.
     * @param  Media|string  $media  The file, or the path of one.
     * @param  array<string, string|int|float|bool|list<string>>  $details  Title, Caption, Keywords, Latitude,
     *                                                                      Longitude, Altitude, Hidden, FileName.
     */
    public function upload(string $album, Media|string $media, array $details = []): ?UploadResult
    {
        return $this->fetch(new UploadImageRequest($album, self::media($media), $details));
    }

    /**
     * Replace an image's file with the uploader, keeping its key and details: POST https://upload.smugmug.com/
     * with X-Smug-ImageUri.
     *
     * @param  string  $album  The key or URI of the album the image is in.
     * @param  string  $imageUri  The image to replace (/api/v2/image/{key}).
     * @param  array<string, string|int|float|bool|list<string>>  $details
     */
    public function replace(string $album, string $imageUri, Media|string $media, array $details = []): ?UploadResult
    {
        return $this->fetch(new UploadImageRequest($album, self::media($media), $details, $imageUri));
    }

    /**
     * Add a file to an album through the API instead of the uploader: POST /api/v2/album/{albumKey}!images.
     *
     * @param  array<string, string|bool|list<string>>  $fields  Title, Caption, Keywords, Hidden, ReplaceMediaUri.
     * @param  Options|array<string, mixed>  $options
     */
    public function toAlbum(string $albumKey, Media|string $media, array $fields = [], Options|array $options = []): mixed
    {
        return $this->fetch(new CreateAlbumImageRequest($albumKey, self::media($media), $fields, $this->options($options)));
    }

    /**
     * Replace an image's file through the API: POST /api/v2/image/{imageKey}!replace.
     *
     * @param  Options|array<string, mixed>  $options
     * @return array<string, mixed>
     */
    public function replaceMedia(string $imageKey, Media|string $media, Options|array $options = []): array
    {
        return $this->fetch(new ReplaceImageMediaRequest($imageKey, self::media($media), $this->options($options)));
    }

    private static function media(Media|string $media): Media
    {
        return $media instanceof Media ? $media : Media::fromPath($media);
    }
}
