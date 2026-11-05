<?php

namespace FredBradley\SmugMug\Requests\Account;

use FredBradley\SmugMug\Data\Account\Subscription;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ItemRequest;
use FredBradley\SmugMug\Support\Path;

/**
 * A subscription, by key: GET /api/v2/subscription/{subscriptionKey}.
 */
class GetSubscriptionRequest extends ItemRequest
{
    protected ?string $dto = Subscription::class;

    /**
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $subscriptionKey, Options|array $options = [])
    {
        $this->setOptions($options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/subscription/'.Path::segment($this->subscriptionKey);
    }
}
