<?php

namespace FredBradley\SmugMug\Requests\Node;

use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\DeleteRequest;
use FredBradley\SmugMug\Support\Path;

/**
 * Remove the pricelist mapped to a node: DELETE /api/v2/node/{nodeId}!pricelist.
 */
class RemoveNodePricelistRequest extends DeleteRequest
{
    /**
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $nodeId, Options|array $options = [])
    {
        $this->setOptions($options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/node/'.Path::segment($this->nodeId).'!pricelist';
    }
}
