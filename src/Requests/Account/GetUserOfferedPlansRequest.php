<?php

namespace FredBradley\SmugMug\Requests\Account;

use FredBradley\SmugMug\Data\Account\Plan;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ListRequest;
use FredBradley\SmugMug\Support\Path;

/**
 * The plans a user may move to: GET /api/v2/user/{nickname}!offeredplans.
 */
class GetUserOfferedPlansRequest extends ListRequest
{
    protected ?string $dto = Plan::class;

    /**
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $nickname, Options|array $options = [])
    {
        $this->setOptions($options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/user/'.Path::segment($this->nickname).'!offeredplans';
    }
}
