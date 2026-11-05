<?php

namespace FredBradley\SmugMug\Resources;

use FredBradley\SmugMug\Data\Social\Template;
use FredBradley\SmugMug\Listing;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\Template\GetTemplateRequest;
use FredBradley\SmugMug\Requests\Template\GetTemplatesRequest;

/**
 * Album display templates, from SmugMug::templates().
 */
class TemplateResource extends BaseResource
{
    /**
     * Every album display template: GET /api/v2/template!templates.
     *
     * @param  Options|array<string, mixed>  $options
     * @return Listing<Template>
     */
    public function all(Options|array $options = []): Listing
    {
        return $this->listing(fn (Options $o) => new GetTemplatesRequest($o), $options);
    }

    /**
     * An album display template, by id: GET /api/v2/template/{templateId}.
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function get(string $templateId, Options|array $options = []): ?Template
    {
        return $this->fetch(new GetTemplateRequest($templateId, $this->options($options)));
    }
}
