<?php

namespace FredBradley\SmugMug\Data\Upload;

use FredBradley\SmugMug\Data\Data;

/**
 * What the uploader answers with: the URIs of the new (or replaced) image and of the image in its album, and its
 * web address. Fetch the image itself with SmugMug::follow($result->ImageUri) once it has finished processing.
 *
 * The uploader sends it under "Image" in its own envelope: {"stat": "ok", "method": "smugmug.images.upload",
 * "Image": {…}}.
 */
readonly class UploadResult extends Data
{
    /**
     * @param  array<string, mixed>  $extra
     */
    public function __construct(
        public ?string $ImageUri = null,
        public ?string $AlbumImageUri = null,
        public ?string $StatusImageReplaceUri = null,
        public ?string $URL = null,
        public array $extra = [],
    ) {}

    /**
     * The new image's key, with its serial (as in jPPKD2c-0), read from ImageUri.
     */
    public function imageKey(): ?string
    {
        if ($this->ImageUri === null || ! preg_match('#/image/([^/!?]+)#', $this->ImageUri, $matches)) {
            return null;
        }

        return $matches[1];
    }
}
