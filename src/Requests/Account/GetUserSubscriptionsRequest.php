<?php

namespace FredBradley\SmugMug\Requests\Account;

use FredBradley\SmugMug\Data\Account\Subscription;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ListRequest;
use FredBradley\SmugMug\Support\Path;

/**
 * A user's subscriptions: GET /api/v2/user/{nickname}!subscriptions.
 */
class GetUserSubscriptionsRequest extends ListRequest
{
    protected ?string $dto = Subscription::class;

    /**
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $nickname, Options|array $options = [])
    {
        $this->setOptions($options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/user/'.Path::segment($this->nickname).'!subscriptions';
    }
}
