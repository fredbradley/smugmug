<?php

namespace FredBradley\SmugMug\Requests\Lookup;

use FredBradley\SmugMug\Data\Node\Node;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ItemRequest;

/**
 * The node that a legacy (pre-node) id became: GET /api/v2/node!oldidlookup.
 *
 * Parameters (in the options):
 *  - ID (required): the numeric id of the legacy category or subcategory
 */
class LookupOldNodeIdRequest extends ItemRequest
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
        return '/api/v2/node!oldidlookup';
    }
}
