<?php

namespace FredBradley\SmugMug\Requests\User;

use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\DeleteRequest;
use FredBradley\SmugMug\Support\Path;

/**
 * Close the authenticated user's account: DELETE /api/v2/user/{nickname}.
 *
 * Parameters (in the options):
 *  - Reason: why the account is being closed (query)
 */
class DeleteUserRequest extends DeleteRequest
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
        return '/api/v2/user/'.Path::segment($this->nickname);
    }
}
