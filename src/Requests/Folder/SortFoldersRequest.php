<?php

namespace FredBradley\SmugMug\Requests\Folder;

use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ActionRequest;
use FredBradley\SmugMug\Support\Path;
use JsonSerializable;

/**
 * Reorder a folder's folders: POST /api/v2/folder/user/{nickname}{path*}!sortfolders.
 *
 * Parameters (in the body):
 *  - MoveUris (required): the folders to move (a list of URIs)
 *  - MoveLocation (required): Before or After
 *  - Uri (required): the folder to move them before or after
 */
class SortFoldersRequest extends ActionRequest
{
    protected array $commaSeparated = ['MoveUris'];

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
        return '/api/v2/folder/user/'.Path::segment($this->nickname).Path::folder($this->path).'!sortfolders';
    }
}
