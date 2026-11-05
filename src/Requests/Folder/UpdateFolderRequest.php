<?php

namespace FredBradley\SmugMug\Requests\Folder;

use FredBradley\SmugMug\Data\Node\Folder;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\WriteRequest;
use FredBradley\SmugMug\Support\Path;
use JsonSerializable;
use Saloon\Enums\Method;

/**
 * Change a folder: PATCH /api/v2/folder/user/{nickname}{path*}.
 *
 * Parameters (in the body):
 *  - Name, UrlName, Description, Keywords
 *  - Privacy: Public, Unlisted or Private
 *  - SecurityType, Password, PasswordHint
 *  - SmugSearchable, WorldSearchable
 *  - SortMethod: SortIndex, Name, DateAdded or DateModified
 *  - SortDirection
 *  - HighlightImageUri
 *  - AutoRename
 */
class UpdateFolderRequest extends WriteRequest
{
    protected Method $method = Method::PATCH;

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
        return '/api/v2/folder/user/'.Path::segment($this->nickname).Path::folder($this->path);
    }
}
