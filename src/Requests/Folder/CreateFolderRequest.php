<?php

namespace FredBradley\SmugMug\Requests\Folder;

use FredBradley\SmugMug\Data\Node\Folder;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\WriteRequest;
use FredBradley\SmugMug\Support\Path;
use JsonSerializable;

/**
 * Create a folder in a folder: POST /api/v2/folder/user/{nickname}{path*}!folders.
 *
 * Parameters (in the body):
 *  - Name (required), UrlName (required)
 *  - Description, Keywords
 *  - Privacy, SecurityType, Password, PasswordHint
 *  - SmugSearchable, WorldSearchable
 *  - SortMethod, SortDirection
 *  - HighlightImageUri
 *  - AutoRename
 */
class CreateFolderRequest extends WriteRequest
{
    protected ?string $dto = Folder::class;

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
        return '/api/v2/folder/user/'.Path::segment($this->nickname).Path::folder($this->path).'!folders';
    }
}
