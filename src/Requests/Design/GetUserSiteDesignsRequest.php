<?php

namespace FredBradley\SmugMug\Requests\Design;

use FredBradley\SmugMug\Data\Design\SiteDesign;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ListRequest;
use FredBradley\SmugMug\Support\Path;

/**
 * A user's site designs: GET /api/v2/user/{nickname}!sitedesigns.
 */
class GetUserSiteDesignsRequest extends ListRequest
{
    protected ?string $dto = SiteDesign::class;

    /**
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $nickname, Options|array $options = [])
    {
        $this->setOptions($options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/user/'.Path::segment($this->nickname).'!sitedesigns';
    }
}
