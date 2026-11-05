<?php

namespace FredBradley\SmugMug\Requests\Node;

use FredBradley\SmugMug\Data\Image\Image;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ItemRequest;
use FredBradley\SmugMug\Support\Path;

/**
 * The image that represents a node: GET /api/v2/highlight/node/{nodeId}.
 */
class GetNodeHighlightImageRequest extends ItemRequest
{
    protected ?string $dto = Image::class;

    /**
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $nodeId, Options|array $options = [])
    {
        $this->setOptions($options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/highlight/node/'.Path::segment($this->nodeId);
    }
}
