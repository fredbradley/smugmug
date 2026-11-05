<?php

namespace FredBradley\SmugMug\Requests\Account;

use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ActionRequest;
use FredBradley\SmugMug\Support\Path;
use JsonSerializable;

/**
 * Cancel a subscription: POST /api/v2/subscription/{subscriptionKey}!cancel.
 *
 * Parameters (in the body):
 *  - Email (required): the account's email address, to confirm
 */
class CancelSubscriptionRequest extends ActionRequest
{
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
        return '/api/v2/subscription/'.Path::segment($this->subscriptionKey).'!cancel';
    }
}
