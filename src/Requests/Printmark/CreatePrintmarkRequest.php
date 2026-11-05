<?php

namespace FredBradley\SmugMug\Requests\Printmark;

use FredBradley\SmugMug\Data\Social\Printmark;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\WriteRequest;
use JsonSerializable;

/**
 * Create a printmark: POST /api/v2/printmark.
 *
 * Parameters (in the body):
 *  - Name
 *  - ImageUri, ImageID or ImageKey: the image to print
 *  - Dissolve: opacity
 *  - Gap: the margin
 *  - Location: where on the print
 *  - Scale: its size
 */
class CreatePrintmarkRequest extends WriteRequest
{
    protected ?string $dto = Printmark::class;

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
        return '/api/v2/printmark';
    }
}
