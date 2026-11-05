<?php

namespace FredBradley\SmugMug\Requests\Order;

use FredBradley\SmugMug\Data\Commerce\Order;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ListRequest;
use FredBradley\SmugMug\Support\Path;

/**
 * The orders a user placed: GET /api/v2/user/{nickname}!orders.
 */
class GetUserOrdersRequest extends ListRequest
{
    protected ?string $dto = Order::class;

    /**
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $nickname, Options|array $options = [])
    {
        $this->setOptions($options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/user/'.Path::segment($this->nickname).'!orders';
    }
}
