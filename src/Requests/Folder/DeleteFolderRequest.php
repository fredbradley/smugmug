<?php

namespace FredBradley\SmugMug\Requests\Folder;

use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\DeleteRequest;
use FredBradley\SmugMug\Support\Path;

/**
 * Delete a folder and everything in it: DELETE /api/v2/folder/user/{nickname}{path*}.
 */
class DeleteFolderRequest extends DeleteRequest
{
    /**
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $nickname, public readonly string $path, Options|array $options = [])
    {
        $this->setOptions($options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/folder/user/'.Path::segment($this->nickname).Path::folder($this->path);
    }
}
