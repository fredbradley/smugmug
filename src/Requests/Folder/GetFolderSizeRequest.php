<?php

namespace FredBradley\SmugMug\Requests\Folder;

use FredBradley\SmugMug\Data\Node\Size;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ItemRequest;
use FredBradley\SmugMug\Support\Path;

/**
 * How much a folder holds: GET /api/v2/folder/user/{nickname}{path*}!size.
 *
 * Parameters (in the options):
 *  - FolderID
 *  - depth: 1 (this folder) or MAX (everything under it)
 */
class GetFolderSizeRequest extends ItemRequest
{
    protected ?string $dto = Size::class;

    /**
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $nickname, public readonly string $path, Options|array $options = [])
    {
        $this->setOptions($options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/folder/user/'.Path::segment($this->nickname).Path::folder($this->path).'!size';
    }
}
