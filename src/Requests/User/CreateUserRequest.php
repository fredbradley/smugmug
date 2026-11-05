<?php

namespace FredBradley\SmugMug\Requests\User;

use FredBradley\SmugMug\Data\User\User;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\WriteRequest;
use JsonSerializable;

/**
 * Create a SmugMug account (for partner applications SmugMug has allowed to): POST /api/v2/user.
 *
 * Parameters (in the body):
 *  - Email (required): the new account's email address
 *  - FirstName (required)
 *  - LastName (required)
 *  - NickName: the nickname (and so the site address) to use
 *  - Password (required)
 *  - PlanUri (required): the URI of the plan to sign up for
 *  - IntentToSell (required): yes, no, maybe or skipped
 *  - TimeZone
 *  - Ureferrer: a referral code
 */
class CreateUserRequest extends WriteRequest
{
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
        return '/api/v2/user';
    }
}
