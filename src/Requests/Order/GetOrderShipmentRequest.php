<?php

namespace FredBradley\SmugMug\Requests\Order;

use FredBradley\SmugMug\Data\Commerce\Shipment;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ItemRequest;
use FredBradley\SmugMug\Support\Path;

/**
 * One shipment of an order: GET /api/v2/order/{orderId}/shipment/{shipmentId}.
 */
class GetOrderShipmentRequest extends ItemRequest
{
    protected ?string $dto = Shipment::class;

    /**
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $orderId, public readonly string $shipmentId, Options|array $options = [])
    {
        $this->setOptions($options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/order/'.Path::segment($this->orderId).'/shipment/'.Path::segment($this->shipmentId);
    }
}
