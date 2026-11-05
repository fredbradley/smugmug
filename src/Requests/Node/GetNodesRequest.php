<?php

namespace FredBradley\SmugMug\Requests\Node;

use FredBradley\SmugMug\Data\Node\Node;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ListRequest;
use FredBradley\SmugMug\Support\Path;

/**
 * Several nodes at once (multi-get), by id: GET /api/v2/node/{nodeIds}.
 */
class GetNodesRequest extends ListRequest
{
    protected ?string $dto = Node::class;

    /**
     * @param  string|list<string>  $nodeIds
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string|array $nodeIds, Options|array $options = [])
    {
        $this->setOptions($options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/node/'.Path::segment($this->nodeIds);
    }
}
