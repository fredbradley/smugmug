<?php

namespace FredBradley\SmugMug\Requests\User;

use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ItemRequest;
use FredBradley\SmugMug\Support\Path;

/**
 * The folder, album or page at a path of a user's site: GET /api/v2/user/{nickname}!urlpathlookup.
 *
 * Parameters (in the options):
 *  - urlpath (required): the path below the user's site, such as /Travel/2024
 */
class LookupUserUrlPathRequest extends ItemRequest
{
    /**
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $nickname, Options|array $options = [])
    {
        $this->setOptions($options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/user/'.Path::segment($this->nickname).'!urlpathlookup';
    }
}
