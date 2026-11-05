<?php

namespace FredBradley\SmugMug\Requests\Lookup;

use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ItemRequest;

/**
 * The folder, album, page or album image a SmugMug web address shows: GET /api/v2!weburilookup.
 *
 * Parameters (in the options):
 *  - WebUri (required): the address of a page of a SmugMug site
 */
class LookupWebUriRequest extends ItemRequest
{
    /**
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(Options|array $options = [])
    {
        $this->setOptions($options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2!weburilookup';
    }
}
