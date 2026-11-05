<?php

namespace FredBradley\SmugMug\Requests\Trash;

use FredBradley\SmugMug\Data\Node\Recovered;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\WriteRequest;
use FredBradley\SmugMug\Support\Path;
use JsonSerializable;

/**
 * Recover a deleted page: POST /api/v2/deleted/page/{nodeId}!recover.
 *
 * Parameters (in the body):
 *  - FolderUri: the folder to recover it into
 */
class RecoverDeletedPageRequest extends WriteRequest
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
        return '/api/v2/deleted/page/'.Path::segment($this->nodeId).'!recover';
    }
}
