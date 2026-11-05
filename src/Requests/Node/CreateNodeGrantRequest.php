<?php

namespace FredBradley\SmugMug\Requests\Node;

use FredBradley\SmugMug\Data\User\Grant;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\WriteRequest;
use FredBradley\SmugMug\Support\Path;
use JsonSerializable;

/**
 * Grant someone access to a node: POST /api/v2/node/{nodeId}!grants.
 *
 * Parameters (in the body):
 *  - ContactUri: the contact to grant access to
 *  - Email: or an email address
 */
class CreateNodeGrantRequest extends WriteRequest
{
    protected ?string $dto = Grant::class;

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
        return '/api/v2/node/'.Path::segment($this->nodeId).'!grants';
    }
}
