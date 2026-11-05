<?php

namespace FredBradley\SmugMug\Requests\Order;

use FredBradley\SmugMug\Data\Commerce\OrderItemContent;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ListRequest;
use FredBradley\SmugMug\Support\Path;

/**
 * The images on a line of an order: GET /api/v2/order/{orderId}/item/{itemId}!contents.
 */
class GetOrderItemContentsRequest extends ListRequest
{
    protected ?string $dto = OrderItemContent::class;

    /**
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $orderId, public readonly string $itemId, Options|array $options = [])
    {
        $this->setOptions($options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/order/'.Path::segment($this->orderId).'/item/'.Path::segment($this->itemId).'!contents';
    }
}
