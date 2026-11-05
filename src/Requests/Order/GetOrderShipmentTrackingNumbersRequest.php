<?php

namespace FredBradley\SmugMug\Requests\Order;

use FredBradley\SmugMug\Data\Commerce\TrackingNumbers;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ListRequest;
use FredBradley\SmugMug\Support\Path;

/**
 * The tracking numbers of a shipment: GET /api/v2/order/{orderId}/shipment/{shipmentId}!trackingnumbers.
 */
class GetOrderShipmentTrackingNumbersRequest extends ListRequest
{
    protected ?string $dto = TrackingNumbers::class;

    /**
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $orderId, public readonly string $shipmentId, Options|array $options = [])
    {
        $this->setOptions($options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/order/'.Path::segment($this->orderId).'/shipment/'.Path::segment($this->shipmentId).'!trackingnumbers';
    }
}
