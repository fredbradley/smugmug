<?php

namespace FredBradley\SmugMug\Requests\Folder;

use FredBradley\SmugMug\Data\Album\Album;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\WriteRequest;
use FredBradley\SmugMug\Support\Path;
use JsonSerializable;

/**
 * Create an album in a folder from an album template: POST /api/v2/folder/user/{nickname}{path*}!albumfromalbumtemplate.
 *
 * Parameters (in the body):
 *  - AlbumTemplateUri (required): the template
 *  - Name, UrlName, Description, Keywords, DownloadPassword, SecurityType, ThemeUri, UploadKey
 *  - AutoRename
 */
class CreateFolderAlbumFromTemplateRequest extends WriteRequest
{
    protected ?string $dto = Album::class;

    /**
     * @param  array<mixed>|JsonSerializable  $body  SmugMug's field names and values, or a model.
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $nickname, public readonly string $path, array|JsonSerializable $body = [], Options|array $options = [])
    {
        $this->setBody($body, $options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/folder/user/'.Path::segment($this->nickname).Path::folder($this->path).'!albumfromalbumtemplate';
    }
}
