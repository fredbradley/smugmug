<?php

namespace FredBradley\SmugMug\Requests\Node;

use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ActionRequest;
use FredBradley\SmugMug\Support\Path;
use JsonSerializable;

/**
 * Create a client download for a node: POST /api/v2/node/{nodeId}!createclientdownload.
 *
 * Parameters (in the body):
 *  - Limit: the most images to include
 */
class CreateNodeClientDownloadRequest extends ActionRequest
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
        return '/api/v2/node/'.Path::segment($this->nodeId).'!createclientdownload';
    }
}
