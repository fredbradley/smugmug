<?php

namespace FredBradley\SmugMug\Requests\Order;

use FredBradley\SmugMug\Data\Commerce\OrderItem;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ItemRequest;
use FredBradley\SmugMug\Support\Path;

/**
 * One line of an order: GET /api/v2/order/{orderId}/item/{itemId}.
 */
class GetOrderItemRequest extends ItemRequest
{
    protected ?string $dto = OrderItem::class;

    /**
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $orderId, public readonly string $itemId, Options|array $options = [])
    {
        $this->setOptions($options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/order/'.Path::segment($this->orderId).'/item/'.Path::segment($this->itemId);
    }
}
