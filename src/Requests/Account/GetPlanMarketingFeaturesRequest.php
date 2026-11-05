<?php

namespace FredBradley\SmugMug\Requests\Account;

use FredBradley\SmugMug\Data\Account\PlanMarketingFeatures;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ItemRequest;
use FredBradley\SmugMug\Support\Path;

/**
 * Which of a plan's features the user has used: GET /api/v2/plan/{planKey}!marketingfeatures.
 */
class GetPlanMarketingFeaturesRequest extends ItemRequest
{
    protected ?string $dto = PlanMarketingFeatures::class;

    /**
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $planKey, Options|array $options = [])
    {
        $this->setOptions($options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/plan/'.Path::segment($this->planKey).'!marketingfeatures';
    }
}
