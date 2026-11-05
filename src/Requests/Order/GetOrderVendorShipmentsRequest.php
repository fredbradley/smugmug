<?php

namespace FredBradley\SmugMug\Requests\Order;

use FredBradley\SmugMug\Data\Commerce\Shipment;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ListRequest;
use FredBradley\SmugMug\Support\Path;

/**
 * The shipments from one vendor of an order: GET /api/v2/order/{orderId}/vendor/{vendor}!shipments.
 */
class GetOrderVendorShipmentsRequest extends ListRequest
{
    protected ?string $dto = Shipment::class;

    /**
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $orderId, public readonly string $vendor, Options|array $options = [])
    {
        $this->setOptions($options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/order/'.Path::segment($this->orderId).'/vendor/'.Path::segment($this->vendor).'!shipments';
    }
}
