<?php

namespace FredBradley\SmugMug\Requests\Folder;

use FredBradley\SmugMug\Data\Node\Folder;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ItemRequest;
use FredBradley\SmugMug\Support\Path;

/**
 * The folder a folder is in: GET /api/v2/folder/user/{nickname}{path*}!parent.
 *
 * Parameters (in the options):
 *  - FolderID
 */
class GetFolderParentRequest extends ItemRequest
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
        return '/api/v2/folder/user/'.Path::segment($this->nickname).Path::folder($this->path).'!parent';
    }
}
