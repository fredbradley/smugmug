<?php

namespace FredBradley\SmugMug\Requests\Node;

use FredBradley\SmugMug\Data\Node\Node;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ListRequest;

/**
 * Search nodes: GET /api/v2/node!search.
 *
 * Parameters (in the options):
 *  - Scope (required): the URI of the user or node to search in
 *  - Text: the text to search for
 *  - SortMethod: Rank or LastUpdated
 *  - SortDirection: Ascending or Descending
 */
class SearchNodesRequest extends ListRequest
{
    protected ?string $dto = Node::class;

    /**
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(Options|array $options = [])
    {
        $this->setOptions($options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/node!search';
    }
}
