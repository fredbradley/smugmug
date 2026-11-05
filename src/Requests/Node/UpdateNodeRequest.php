<?php

namespace FredBradley\SmugMug\Requests\Node;

use FredBradley\SmugMug\Data\Node\Node;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\WriteRequest;
use FredBradley\SmugMug\Support\Path;
use JsonSerializable;
use Saloon\Enums\Method;

/**
 * Change a node: PATCH /api/v2/node/{nodeId}.
 *
 * Parameters (in the body):
 *  - Name, UrlName, Description, Keywords (a list)
 *  - Privacy: Public, Unlisted or Private
 *  - SecurityType: None, Password or GrantAccess
 *  - Password, PasswordHint
 *  - SmugSearchable: No, Local, LocalUser, Yes or Inherit from User
 *  - WorldSearchable: No, HomeOnly, Yes or Inherit from User
 *  - SortMethod: SortIndex, Name, DateAdded or DateModified
 *  - SortDirection: Ascending or Descending
 *  - HighlightImageUri, CoverImageUri, ShowCoverImage: the images that represent it
 *  - HideOwner
 *  - AlbumTemplateUri
 *  - AutoRename: rename instead of failing when the UrlName is taken
 *  - ResetUrlName: make the UrlName from the Name again
 */
class UpdateNodeRequest extends WriteRequest
{
    protected Method $method = Method::PATCH;

    protected ?string $dto = Node::class;

    /**
     * @param  array<mixed>|JsonSerializable  $body  SmugMug's field names and values, or a model.
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $nodeId, array|JsonSerializable $body = [], Options|array $options = [])
    {
        $this->setBody($body, $options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/node/'.Path::segment($this->nodeId);
    }
}
