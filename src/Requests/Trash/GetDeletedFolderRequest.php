<?php

namespace FredBradley\SmugMug\Requests\Trash;

use FredBradley\SmugMug\Data\Node\DeletedNode;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ItemRequest;
use FredBradley\SmugMug\Support\Path;

/**
 * A deleted folder, by node id: GET /api/v2/deleted/folder/{nodeId}.
 */
class GetDeletedFolderRequest extends ItemRequest
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
        return '/api/v2/deleted/folder/'.Path::segment($this->nodeId);
    }
}
