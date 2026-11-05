<?php

namespace FredBradley\SmugMug\Requests\Node;

use FredBradley\SmugMug\Data\Commerce\Pricelist;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\WriteRequest;
use FredBradley\SmugMug\Support\Path;
use JsonSerializable;

/**
 * Map a pricelist to a node: POST /api/v2/node/{nodeId}!pricelist.
 *
 * Parameters (in the body):
 *  - PricelistUri (required): the pricelist to map
 */
class SetNodePricelistRequest extends WriteRequest
{
    protected ?string $dto = Pricelist::class;

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
        return '/api/v2/node/'.Path::segment($this->nodeId).'!pricelist';
    }
}
