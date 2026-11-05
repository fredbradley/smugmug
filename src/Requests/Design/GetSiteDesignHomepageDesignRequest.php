<?php

namespace FredBradley\SmugMug\Requests\Design;

use FredBradley\SmugMug\Data\Design\PageDesign;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ItemRequest;
use FredBradley\SmugMug\Support\Path;

/**
 * The design of a site design's homepage: GET /api/v2/sitedesign/{siteDesignKey}!homepagedesign.
 */
class GetSiteDesignHomepageDesignRequest extends ItemRequest
{
    protected ?string $dto = PageDesign::class;

    /**
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $siteDesignKey, Options|array $options = [])
    {
        $this->setOptions($options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/sitedesign/'.Path::segment($this->siteDesignKey).'!homepagedesign';
    }
}
