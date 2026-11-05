<?php

namespace FredBradley\SmugMug\Requests\Watermark;

use FredBradley\SmugMug\Data\Social\Watermark;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\WriteRequest;
use JsonSerializable;

/**
 * Create a watermark: POST /api/v2/watermark.
 *
 * Parameters (in the body):
 *  - Name (required)
 *  - ImageID (required): the numeric id of the image to use
 *  - ImageUri: or its URI
 *  - Dissolve: opacity, 0 to 100
 *  - Pinned: Center, TopLeft, TopRight, BottomLeft, BottomRight, Tile, Top, Left, Right or Bottom
 *  - SmallestSize: Th, S, M, L or XL, the smallest size watermarked
 */
class CreateWatermarkRequest extends WriteRequest
{
    protected ?string $dto = Watermark::class;

    /**
     * @param  array<mixed>|JsonSerializable  $body  SmugMug's field names and values, or a model.
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(array|JsonSerializable $body = [], Options|array $options = [])
    {
        $this->setBody($body, $options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/watermark';
    }
}
