<?php

namespace FredBradley\SmugMug\Requests\Design;

use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ActionRequest;
use FredBradley\SmugMug\Support\Path;
use JsonSerializable;

/**
 * Copy a site design: POST /api/v2/sitedesign/{siteDesignKey}!clone.
 */
class CloneSiteDesignRequest extends ActionRequest
{
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
        return '/api/v2/sitedesign/'.Path::segment($this->siteDesignKey).'!clone';
    }
}
