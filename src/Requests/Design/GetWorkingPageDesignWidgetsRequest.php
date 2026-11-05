<?php

namespace FredBradley\SmugMug\Requests\Design;

use FredBradley\SmugMug\Data\Design\Widget;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ListRequest;
use FredBradley\SmugMug\Support\Path;

/**
 * The widgets on the working version of a page design: GET /api/v2/pagedesign/{pageDesignId}/working!widgets.
 */
class GetWorkingPageDesignWidgetsRequest extends ListRequest
{
    protected ?string $dto = Widget::class;

    /**
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $pageDesignId, Options|array $options = [])
    {
        $this->setOptions($options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/pagedesign/'.Path::segment($this->pageDesignId).'/working!widgets';
    }
}
