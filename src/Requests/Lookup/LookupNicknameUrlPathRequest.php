<?php

namespace FredBradley\SmugMug\Requests\Lookup;

use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ItemRequest;

/**
 * The folder, album or page at a user's path: GET /api/v2!nicknameurlpathlookup.
 *
 * Parameters (in the options):
 *  - nickname (required): the user
 *  - urlpath (required): the path below their site
 */
class LookupNicknameUrlPathRequest extends ItemRequest
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
        return '/api/v2!nicknameurlpathlookup';
    }
}
