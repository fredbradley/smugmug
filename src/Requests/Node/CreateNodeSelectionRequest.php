<?php

namespace FredBradley\SmugMug\Requests\Node;

use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ActionRequest;
use FredBradley\SmugMug\Support\Path;
use JsonSerializable;

/**
 * Create a selection (an album of chosen images) under a node: POST /api/v2/node/{nodeId}!createselection.
 *
 * Parameters (in the body):
 *  - AlbumImageUris: the album images to select
 *  - SearchText, SearchSort: or a search
 *  - Limit: the most images to select
 */
class CreateNodeSelectionRequest extends ActionRequest
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
        return '/api/v2/node/'.Path::segment($this->nodeId).'!createselection';
    }
}
