<?php

namespace FredBradley\SmugMug\Requests\User;

use FredBradley\SmugMug\Data\User\User;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\WriteRequest;
use JsonSerializable;
use Saloon\Enums\Method;
use Saloon\Http\PendingRequest;

/**
 * Change the authenticated user's nickname: PATCH /api/v2!authuser. Needs an access token.
 *
 * Parameters (in the body):
 *  - NickName: a new nickname
 */
class UpdateAuthUserRequest extends WriteRequest
{
    protected Method $method = Method::PATCH;

    protected ?string $dto = User::class;

    /**
     * @param  array<mixed>|JsonSerializable  $body  SmugMug's field names and values, or a model.
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(array|JsonSerializable $body = [], Options|array $options = [])
    {
        $this->setBody($body, $options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2!authuser';
    }

    public function boot(PendingRequest $pendingRequest): void
    {
        parent::boot($pendingRequest);

        self::requireToken($pendingRequest, 'Changing /api/v2!authuser (users()->updateMe())');
    }
}
