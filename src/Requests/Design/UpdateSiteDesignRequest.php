<?php

namespace FredBradley\SmugMug\Requests\Design;

use FredBradley\SmugMug\Data\Design\SiteDesign;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\WriteRequest;
use FredBradley\SmugMug\Support\Path;
use JsonSerializable;
use Saloon\Enums\Method;

/**
 * Change where a site design appears in the design picker: PATCH /api/v2/sitedesign/{siteDesignKey}.
 *
 * Parameters (in the body):
 *  - DesignPickerOrder, DesignPickerGroupName (Showcase Gallery, Slideshow, Photo Grid, Photo Collage, Photo Column or Spotlight Photo)
 */
class UpdateSiteDesignRequest extends WriteRequest
{
    protected Method $method = Method::PATCH;

    protected ?string $dto = SiteDesign::class;

    /**
     * @param  array<mixed>|JsonSerializable  $body  SmugMug's field names and values, or a model.
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $siteDesignKey, array|JsonSerializable $body = [], Options|array $options = [])
    {
        $this->setBody($body, $options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/sitedesign/'.Path::segment($this->siteDesignKey);
    }
}
