<?php

namespace FredBradley\SmugMug\Requests\Page;

use FredBradley\SmugMug\Data\Node\Page;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ItemRequest;
use FredBradley\SmugMug\Support\Path;

/**
 * A page, by key: GET /api/v2/page/{pageKey}.
 */
class GetPageRequest extends ItemRequest
{
    protected ?string $dto = Page::class;

    /**
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $pageKey, Options|array $options = [])
    {
        $this->setOptions($options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/page/'.Path::segment($this->pageKey);
    }
}
