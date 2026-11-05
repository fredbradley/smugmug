<?php

namespace FredBradley\SmugMug\Requests\Folder;

use FredBradley\SmugMug\Data\Album\AlbumListItem;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ListRequest;
use FredBradley\SmugMug\Support\Path;

/**
 * Every album under a folder, as a flat list: GET /api/v2/folder/user/{nickname}{path*}!albumlist.
 *
 * Parameters (in the options):
 *  - FolderID
 */
class GetFolderAlbumListRequest extends ListRequest
{
    protected ?string $dto = AlbumListItem::class;

    /**
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $nickname, public readonly string $path, Options|array $options = [])
    {
        $this->setOptions($options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/folder/user/'.Path::segment($this->nickname).Path::folder($this->path).'!albumlist';
    }
}
