<?php

namespace FredBradley\SmugMug\Requests\Template;

use FredBradley\SmugMug\Data\Social\Template;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ItemRequest;
use FredBradley\SmugMug\Support\Path;

/**
 * An album display template, by id: GET /api/v2/template/{templateId}.
 */
class GetTemplateRequest extends ItemRequest
{
    protected ?string $dto = Template::class;

    /**
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $templateId, Options|array $options = [])
    {
        $this->setOptions($options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/template/'.Path::segment($this->templateId);
    }
}
