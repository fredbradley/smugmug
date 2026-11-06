<?php

namespace FredBradley\SmugMug\Requests\User;

use FredBradley\SmugMug\Data\User\User;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ItemRequest;
use Saloon\Http\PendingRequest;

/**
 * The user the access token belongs to: GET /api/v2!authuser. Needs an access token: without one it throws a
 * LogicException rather than sending a request SmugMug would answer with 404.
 */
class GetAuthUserRequest extends ItemRequest
{
    protected ?string $dto = User::class;

    /**
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(Options|array $options = [])
    {
        $this->setOptions($options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2!authuser';
    }

    public function boot(PendingRequest $pendingRequest): void
    {
        parent::boot($pendingRequest);

        self::requireToken($pendingRequest, 'Fetching /api/v2!authuser (users()->me())');
    }
}
