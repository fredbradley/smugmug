<?php

namespace FredBradley\SmugMug\Requests\Node;

use FredBradley\SmugMug\Data\Node\Node;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\WriteRequest;
use FredBradley\SmugMug\Support\Path;
use JsonSerializable;

/**
 * Create a folder, album or page in a folder node: POST /api/v2/node/{nodeId}!children.
 *
 * Parameters (in the body):
 *  - Type (required): Album, Folder or Page
 *  - Name (required)
 *  - UrlName: the address part (made from the Name when left out)
 *  - Description, Keywords (a list)
 *  - Privacy: Public, Unlisted or Private
 *  - SecurityType, Password, PasswordHint
 *  - SmugSearchable, WorldSearchable
 *  - SortMethod, SortDirection
 *  - HighlightImageUri, CoverImageUri, ShowCoverImage, HideOwner
 *  - AlbumTemplateUri: an album template to create an album from
 *  - AutoRename: rename instead of failing when the UrlName is taken
 */
class CreateNodeRequest extends WriteRequest
{
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
        return '/api/v2/node/'.Path::segment($this->nodeId).'!children';
    }
}
