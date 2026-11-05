<?php

namespace FredBradley\SmugMug\Requests\Folder;

use FredBradley\SmugMug\Data\Node\Page;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\WriteRequest;
use FredBradley\SmugMug\Support\Path;
use JsonSerializable;

/**
 * Create a page in a folder: POST /api/v2/folder/user/{nickname}{path*}!pages.
 *
 * Parameters (in the body):
 *  - Name (required), UrlName (required)
 *  - Privacy, SecurityType, Password, PasswordHint
 *  - AutoRename
 */
class CreateFolderPageRequest extends WriteRequest
{
    protected ?string $dto = Page::class;

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
        return '/api/v2/folder/user/'.Path::segment($this->nickname).Path::folder($this->path).'!pages';
    }
}
