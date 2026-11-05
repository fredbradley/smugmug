<?php

namespace FredBradley\SmugMug\Requests\Account;

use FredBradley\SmugMug\Data\Account\Subscription;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\WriteRequest;
use FredBradley\SmugMug\Support\Path;
use JsonSerializable;
use Saloon\Enums\Method;

/**
 * Change a subscription: PATCH /api/v2/subscription/{subscriptionKey}.
 *
 * Parameters (in the body):
 *  - NumberOfSeats, ConsentVersion
 */
class UpdateSubscriptionRequest extends WriteRequest
{
    protected Method $method = Method::PATCH;

    protected ?string $dto = Subscription::class;

    /**
     * @param  array<mixed>|JsonSerializable  $body  SmugMug's field names and values, or a model.
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $subscriptionKey, array|JsonSerializable $body = [], Options|array $options = [])
    {
        $this->setBody($body, $options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/subscription/'.Path::segment($this->subscriptionKey);
    }
}
