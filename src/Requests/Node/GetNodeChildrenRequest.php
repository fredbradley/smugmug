<?php

namespace FredBradley\SmugMug\Requests\Node;

use FredBradley\SmugMug\Data\Node\Node;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ListRequest;
use FredBradley\SmugMug\Support\Path;

/**
 * The folders, albums and pages in a folder node: GET /api/v2/node/{nodeId}!children.
 *
 * Parameters (in the options):
 *  - Type: All, Folder Album Page, Folder, Album, Page or System Album
 *  - SortMethod: DateAdded, DateModified, Name or Organizer
 *  - SortDirection: Ascending or Descending
 */
class GetNodeChildrenRequest extends ListRequest
{
    protected ?string $dto = Node::class;

    /**
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $nodeId, Options|array $options = [])
    {
        $this->setOptions($options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/node/'.Path::segment($this->nodeId).'!children';
    }
}
