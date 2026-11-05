<?php

namespace FredBradley\SmugMug\Requests\Sales;

use FredBradley\SmugMug\Data\Commerce\Shipment;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ListRequest;
use FredBradley\SmugMug\Support\Path;

/**
 * The shipments of a sales order: GET /api/v2/sales/{nickname}/order/{orderId}!shipments.
 */
class GetSalesOrderShipmentsRequest extends ListRequest
{
    protected ?string $dto = Shipment::class;

    /**
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $nickname, public readonly string $orderId, Options|array $options = [])
    {
        $this->setOptions($options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/sales/'.Path::segment($this->nickname).'/order/'.Path::segment($this->orderId).'!shipments';
    }
}
