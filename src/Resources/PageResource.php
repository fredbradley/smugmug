<?php

namespace FredBradley\SmugMug\Resources;

use FredBradley\SmugMug\Data\Node\Page;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\Page\GetPageRequest;

/**
 * Pages, from SmugMug::pages().
 */
class PageResource extends BaseResource
{
    /**
     * A page, by key: GET /api/v2/page/{pageKey}.
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function get(string $pageKey, Options|array $options = []): ?Page
    {
        return $this->fetch(new GetPageRequest($pageKey, $this->options($options)));
    }
}
