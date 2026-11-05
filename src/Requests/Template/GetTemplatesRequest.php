<?php

namespace FredBradley\SmugMug\Requests\Template;

use FredBradley\SmugMug\Data\Social\Template;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ListRequest;

/**
 * Every album display template: GET /api/v2/template!templates.
 */
class GetTemplatesRequest extends ListRequest
{
    protected ?string $dto = Template::class;

    /**
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(Options|array $options = [])
    {
        $this->setOptions($options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/template!templates';
    }
}
