<?php

namespace FredBradley\SmugMug\Requests\Account;

use FredBradley\SmugMug\Data\Account\Plan;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ItemRequest;
use FredBradley\SmugMug\Support\Path;

/**
 * A plan, by key: GET /api/v2/plan/{planKey}.
 *
 * Parameters (in the options):
 *  - Currency
 */
class GetPlanRequest extends ItemRequest
{
    protected ?string $dto = Plan::class;

    /**
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $planKey, Options|array $options = [])
    {
        $this->setOptions($options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/plan/'.Path::segment($this->planKey);
    }
}
