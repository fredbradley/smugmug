<?php

namespace FredBradley\SmugMug\Requests\Trash;

use FredBradley\SmugMug\Data\Node\DeletedNode;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ItemRequest;
use FredBradley\SmugMug\Support\Path;

/**
 * A deleted album, by node id: GET /api/v2/deleted/album/{nodeId}.
 */
class GetDeletedAlbumRequest extends ItemRequest
{
    protected ?string $dto = DeletedNode::class;

    /**
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $nodeId, Options|array $options = [])
    {
        $this->setOptions($options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/deleted/album/'.Path::segment($this->nodeId);
    }
}
