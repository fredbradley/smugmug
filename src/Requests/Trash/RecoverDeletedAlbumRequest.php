<?php

namespace FredBradley\SmugMug\Requests\Trash;

use FredBradley\SmugMug\Data\Node\Recovered;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\WriteRequest;
use FredBradley\SmugMug\Support\Path;
use JsonSerializable;

/**
 * Recover a deleted album: POST /api/v2/deleted/album/{nodeId}!recover.
 *
 * Parameters (in the body):
 *  - FolderUri: the folder to recover it into (where it was by default)
 *  - IncludeChildren: recover its images too
 */
class RecoverDeletedAlbumRequest extends WriteRequest
{
    protected ?string $dto = Recovered::class;

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
        return '/api/v2/deleted/album/'.Path::segment($this->nodeId).'!recover';
    }
}
