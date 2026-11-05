<?php

namespace FredBradley\SmugMug\Resources;

use FredBradley\SmugMug\Data\Account\ApiKey;
use FredBradley\SmugMug\Data\Account\Component;
use FredBradley\SmugMug\Data\Account\Invoice;
use FredBradley\SmugMug\Data\Account\Label;
use FredBradley\SmugMug\Data\Account\Plan;
use FredBradley\SmugMug\Data\Account\PlanMarketingFeatures;
use FredBradley\SmugMug\Data\Account\Subscription;
use FredBradley\SmugMug\Data\Data;
use FredBradley\SmugMug\Listing;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\Account\CancelSubscriptionRequest;
use FredBradley\SmugMug\Requests\Account\CreateApiKeyRequest;
use FredBradley\SmugMug\Requests\Account\GetApiKeyRequest;
use FredBradley\SmugMug\Requests\Account\GetInvoiceRequest;
use FredBradley\SmugMug\Requests\Account\GetLibraryAssetComponentRequest;
use FredBradley\SmugMug\Requests\Account\GetLibraryAssetComponentsRequest;
use FredBradley\SmugMug\Requests\Account\GetLibraryAssetLabelsRequest;
use FredBradley\SmugMug\Requests\Account\GetPlanMarketingFeaturesRequest;
use FredBradley\SmugMug\Requests\Account\GetPlanRequest;
use FredBradley\SmugMug\Requests\Account\GetSubscriptionInvoicesRequest;
use FredBradley\SmugMug\Requests\Account\GetSubscriptionRequest;
use FredBradley\SmugMug\Requests\Account\GetUserApiKeysRequest;
use FredBradley\SmugMug\Requests\Account\GetUserOfferedPlansRequest;
use FredBradley\SmugMug\Requests\Account\GetUserSubscriptionsRequest;
use FredBradley\SmugMug\Requests\Account\UpdateApiKeyRequest;
use FredBradley\SmugMug\Requests\Account\UpdateSubscriptionRequest;

/**
 * API keys, plans, subscriptions, invoices and the library, from SmugMug::account().
 */
class AccountResource extends BaseResource
{
    /**
     * A user's API keys: GET /api/v2/user/{nickname}!apikeys.
     *
     * @param  Options|array<string, mixed>  $options
     * @return Listing<ApiKey>
     */
    public function apiKeys(string $nickname, Options|array $options = []): Listing
    {
        return $this->listing(fn (Options $o) => new GetUserApiKeysRequest($nickname, $o), $options);
    }

    /**
     * Apply for an API key: POST /api/v2/user/{nickname}!apikeys.
     *
     *  - Name (required)
     *  - Type (required): Application, Plug-In, Service, Toy, Other or MCP Server / Agentic AI
     *  - Platform (required): Windows, Macintosh, Linux, Mobile, All or Other
     *  - UsedFor (required): Non-Commercial or Commercial
     *  - Description (required)
     *  - AcceptTermsOfService (required): true
     *  - AppUrl, CallbackUrl
     *
     * @param  array<string, mixed>|Data  $fields
     * @param  Options|array<string, mixed>  $options
     */
    public function createApiKey(string $nickname, array|Data $fields, Options|array $options = []): ?ApiKey
    {
        return $this->fetch(new CreateApiKeyRequest($nickname, $fields, $this->options($options)));
    }

    /**
     * An API key, by id: GET /api/v2/apikey/{apiKeyId}.
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function apiKey(string $apiKeyId, Options|array $options = []): ?ApiKey
    {
        return $this->fetch(new GetApiKeyRequest($apiKeyId, $this->options($options)));
    }

    /**
     * Change an API key's name or addresses: PATCH /api/v2/apikey/{apiKeyId}.
     *
     *  - Name, AppUrl, CallbackUrl
     *
     * @param  array<string, mixed>|Data  $fields
     * @param  Options|array<string, mixed>  $options
     */
    public function updateApiKey(string $apiKeyId, array|Data $fields, Options|array $options = []): ?ApiKey
    {
        return $this->fetch(new UpdateApiKeyRequest($apiKeyId, $fields, $this->options($options)));
    }

    /**
     * The plans a user may move to: GET /api/v2/user/{nickname}!offeredplans.
     *
     * @param  Options|array<string, mixed>  $options
     * @return Listing<Plan>
     */
    public function offeredPlans(string $nickname, Options|array $options = []): Listing
    {
        return $this->listing(fn (Options $o) => new GetUserOfferedPlansRequest($nickname, $o), $options);
    }

    /**
     * A plan, by key: GET /api/v2/plan/{planKey}.
     *
     *  - Currency
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function plan(string $planKey, Options|array $options = []): ?Plan
    {
        return $this->fetch(new GetPlanRequest($planKey, $this->options($options)));
    }

    /**
     * Which of a plan's features the user has used: GET /api/v2/plan/{planKey}!marketingfeatures.
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function planMarketingFeatures(string $planKey, Options|array $options = []): ?PlanMarketingFeatures
    {
        return $this->fetch(new GetPlanMarketingFeaturesRequest($planKey, $this->options($options)));
    }

    /**
     * A user's subscriptions: GET /api/v2/user/{nickname}!subscriptions.
     *
     * @param  Options|array<string, mixed>  $options
     * @return Listing<Subscription>
     */
    public function subscriptions(string $nickname, Options|array $options = []): Listing
    {
        return $this->listing(fn (Options $o) => new GetUserSubscriptionsRequest($nickname, $o), $options);
    }

    /**
     * A subscription, by key: GET /api/v2/subscription/{subscriptionKey}.
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function subscription(string $subscriptionKey, Options|array $options = []): ?Subscription
    {
        return $this->fetch(new GetSubscriptionRequest($subscriptionKey, $this->options($options)));
    }

    /**
     * Change a subscription: PATCH /api/v2/subscription/{subscriptionKey}.
     *
     *  - NumberOfSeats, ConsentVersion
     *
     * @param  array<string, mixed>|Data  $fields
     * @param  Options|array<string, mixed>  $options
     */
    public function updateSubscription(string $subscriptionKey, array|Data $fields, Options|array $options = []): ?Subscription
    {
        return $this->fetch(new UpdateSubscriptionRequest($subscriptionKey, $fields, $this->options($options)));
    }

    /**
     * Cancel a subscription: POST /api/v2/subscription/{subscriptionKey}!cancel.
     *
     *  - Email (required): the account's email address, to confirm
     *
     * @param  array<string, mixed>  $parameters
     * @param  Options|array<string, mixed>  $options
     * @return array<string, mixed>
     */
    public function cancelSubscription(string $subscriptionKey, array $parameters = [], Options|array $options = []): array
    {
        return $this->fetch(new CancelSubscriptionRequest($subscriptionKey, $parameters, $this->options($options)));
    }

    /**
     * A subscription's invoices: GET /api/v2/subscription/{subscriptionKey}!invoices.
     *
     *  - InvoiceStatus: Draft, Open, Paid, Uncollectible or Void
     *
     * @param  Options|array<string, mixed>  $options
     * @return Listing<Invoice>
     */
    public function invoices(string $subscriptionKey, Options|array $options = []): Listing
    {
        return $this->listing(fn (Options $o) => new GetSubscriptionInvoicesRequest($subscriptionKey, $o), $options);
    }

    /**
     * An invoice, by key: GET /api/v2/invoice/{invoiceKey}.
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function invoice(string $invoiceKey, Options|array $options = []): ?Invoice
    {
        return $this->fetch(new GetInvoiceRequest($invoiceKey, $this->options($options)));
    }

    /**
     * The labels on a library asset: GET /api/v2/library/asset/{assetKey}!labels.
     *
     * @param  Options|array<string, mixed>  $options
     * @return Listing<Label>
     */
    public function assetLabels(string $assetKey, Options|array $options = []): Listing
    {
        return $this->listing(fn (Options $o) => new GetLibraryAssetLabelsRequest($assetKey, $o), $options);
    }

    /**
     * The files of a library asset: GET /api/v2/library/asset/{assetKey}!components.
     *
     * @param  Options|array<string, mixed>  $options
     * @return Listing<Component>
     */
    public function assetComponents(string $assetKey, Options|array $options = []): Listing
    {
        return $this->listing(fn (Options $o) => new GetLibraryAssetComponentsRequest($assetKey, $o), $options);
    }

    /**
     * One file of a library asset: GET /api/v2/library/asset/{assetKey}/component/{componentType}/{componentKey}.
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function assetComponent(string $assetKey, string $componentType, string $componentKey, Options|array $options = []): ?Component
    {
        return $this->fetch(new GetLibraryAssetComponentRequest($assetKey, $componentType, $componentKey, $this->options($options)));
    }
}
