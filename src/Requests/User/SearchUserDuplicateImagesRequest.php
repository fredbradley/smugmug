<?php

namespace FredBradley\SmugMug\Requests\User;

use FredBradley\SmugMug\Data\Image\Image;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ListRequest;
use FredBradley\SmugMug\Support\Path;

/**
 * Images in an album with the same file name, to avoid uploading one twice: GET /api/v2/user/{nickname}!duplicateimagesearch.
 *
 * Parameters (in the options):
 *  - AlbumUri (required): the album to look in
 *  - FileName (required): the file name to look for
 */
class SearchUserDuplicateImagesRequest extends ListRequest
{
    protected ?string $dto = Image::class;

    /**
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $nickname, Options|array $options = [])
    {
        $this->setOptions($options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/user/'.Path::segment($this->nickname).'!duplicateimagesearch';
    }
}
