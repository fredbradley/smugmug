<?php

namespace FredBradley\SmugMug\Requests\Account;

use FredBradley\SmugMug\Data\Account\Invoice;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ListRequest;
use FredBradley\SmugMug\Support\Path;

/**
 * A subscription's invoices: GET /api/v2/subscription/{subscriptionKey}!invoices.
 *
 * Parameters (in the options):
 *  - InvoiceStatus: Draft, Open, Paid, Uncollectible or Void
 */
class GetSubscriptionInvoicesRequest extends ListRequest
{
    protected ?string $dto = Invoice::class;

    /**
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $subscriptionKey, Options|array $options = [])
    {
        $this->setOptions($options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/subscription/'.Path::segment($this->subscriptionKey).'!invoices';
    }
}
