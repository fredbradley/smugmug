<?php

namespace FredBradley\SmugMug\Requests\User;

use FredBradley\SmugMug\Data\User\User;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\WriteRequest;
use FredBradley\SmugMug\Support\Path;
use JsonSerializable;
use Saloon\Enums\Method;

/**
 * Change the authenticated user's account settings: PATCH /api/v2/user/{nickname}.
 *
 * Parameters (in the body):
 *  - TimeZone: the user's time zone, such as Europe/London
 *  - Email: the account's email address
 *  - MfaEnabled: whether two-factor authentication is on
 *  - Password: the current password, needed to change the email or password
 *  - NewPassword: the new password
 */
class UpdateUserRequest extends WriteRequest
{
    protected Method $method = Method::PATCH;

    protected ?string $dto = User::class;

    /**
     * @param  array<mixed>|JsonSerializable  $body  SmugMug's field names and values, or a model.
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $nickname, array|JsonSerializable $body = [], Options|array $options = [])
    {
        $this->setBody($body, $options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/user/'.Path::segment($this->nickname);
    }
}
