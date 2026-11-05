<?php

namespace FredBradley\SmugMug\Requests\Node;

use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ActionRequest;
use FredBradley\SmugMug\Support\Path;
use JsonSerializable;

/**
 * Move nodes into a folder node: POST /api/v2/node/{nodeId}!movenodes.
 *
 * Parameters (in the body):
 *  - MoveUris (required): the nodes to move into this folder node (a list of URIs)
 *  - AutoRename: rename instead of failing on a clash
 *  - Async: true to do it in the background
 */
class MoveNodesRequest extends ActionRequest
{
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
        return '/api/v2/node/'.Path::segment($this->nodeId).'!movenodes';
    }
}
