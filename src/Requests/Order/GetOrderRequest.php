<?php

namespace FredBradley\SmugMug\Requests\Order;

use FredBradley\SmugMug\Data\Commerce\Order;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ItemRequest;
use FredBradley\SmugMug\Support\Path;

/**
 * An order, by id: GET /api/v2/order/{orderId}.
 */
class GetOrderRequest extends ItemRequest
{
    protected ?string $dto = Order::class;

    /**
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $orderId, Options|array $options = [])
    {
        $this->setOptions($options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/order/'.Path::segment($this->orderId);
    }
}
