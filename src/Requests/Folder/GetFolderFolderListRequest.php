<?php

namespace FredBradley\SmugMug\Requests\Folder;

use FredBradley\SmugMug\Data\Node\FolderListItem;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ListRequest;
use FredBradley\SmugMug\Support\Path;

/**
 * Every folder under a folder, as a flat list: GET /api/v2/folder/user/{nickname}{path*}!folderlist.
 *
 * Parameters (in the options):
 *  - FolderID
 */
class GetFolderFolderListRequest extends ListRequest
{
    protected ?string $dto = FolderListItem::class;

    /**
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $nickname, public readonly string $path, Options|array $options = [])
    {
        $this->setOptions($options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/folder/user/'.Path::segment($this->nickname).Path::folder($this->path).'!folderlist';
    }
}
