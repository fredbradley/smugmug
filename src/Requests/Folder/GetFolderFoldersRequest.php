<?php

namespace FredBradley\SmugMug\Requests\Folder;

use FredBradley\SmugMug\Data\Node\Folder;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ListRequest;
use FredBradley\SmugMug\Support\Path;

/**
 * The folders in a folder: GET /api/v2/folder/user/{nickname}{path*}!folders.
 *
 * Parameters (in the options):
 *  - FolderID
 */
class GetFolderFoldersRequest extends ListRequest
{
    protected ?string $dto = Folder::class;

    /**
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $nickname, public readonly string $path, Options|array $options = [])
    {
        $this->setOptions($options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/folder/user/'.Path::segment($this->nickname).Path::folder($this->path).'!folders';
    }
}
