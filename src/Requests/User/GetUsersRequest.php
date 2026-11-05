<?php

namespace FredBradley\SmugMug\Requests\User;

use FredBradley\SmugMug\Data\User\User;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ListRequest;
use FredBradley\SmugMug\Support\Path;

/**
 * Several users at once (SmugMug's multi-get), by nickname: GET /api/v2/user/{nicknames}.
 */
class GetUsersRequest extends ListRequest
{
    protected ?string $dto = User::class;

    /**
     * @param  string|list<string>  $nicknames
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string|array $nicknames, Options|array $options = [])
    {
        $this->setOptions($options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/user/'.Path::segment($this->nicknames);
    }
}
