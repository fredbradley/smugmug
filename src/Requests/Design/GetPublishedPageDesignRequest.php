<?php

namespace FredBradley\SmugMug\Requests\Design;

use FredBradley\SmugMug\Data\Design\PageDesign;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ItemRequest;
use FredBradley\SmugMug\Support\Path;

/**
 * The published version of a page design: GET /api/v2/pagedesign/{pageDesignId}/published.
 */
class GetPublishedPageDesignRequest extends ItemRequest
{
    protected ?string $dto = PageDesign::class;

    /**
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $pageDesignId, Options|array $options = [])
    {
        $this->setOptions($options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/pagedesign/'.Path::segment($this->pageDesignId).'/published';
    }
}
