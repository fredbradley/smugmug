<?php

namespace FredBradley\SmugMug\Requests\Album;

use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ActionRequest;
use FredBradley\SmugMug\Support\Path;
use JsonSerializable;

/**
 * Have SmugMug fetch a file from an address into an album: POST /api/v2/album/{albumKey}!uploadfromuri.
 *
 * Parameters (in the body):
 *  - Uri (required): the address SmugMug fetches the file from
 *  - FileName, Title, Caption, Keywords, Hidden: the new image's details
 *  - ByteCount, MD5Sum: to check the file
 *  - Cookie: a cookie to send with the fetch
 *  - AllowInsecure: allow an http address
 */
class UploadAlbumImageFromUriRequest extends ActionRequest
{
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
        return '/api/v2/album/'.Path::segment($this->albumKey).'!uploadfromuri';
    }
}
