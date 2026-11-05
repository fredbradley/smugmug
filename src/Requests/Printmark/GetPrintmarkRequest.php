<?php

namespace FredBradley\SmugMug\Requests\Printmark;

use FredBradley\SmugMug\Data\Social\Printmark;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ItemRequest;
use FredBradley\SmugMug\Support\Path;

/**
 * A printmark, by id: GET /api/v2/printmark/{printmarkId}.
 */
class GetPrintmarkRequest extends ItemRequest
{
    protected ?string $dto = Printmark::class;

    /**
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $printmarkId, Options|array $options = [])
    {
        $this->setOptions($options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/printmark/'.Path::segment($this->printmarkId);
    }
}
