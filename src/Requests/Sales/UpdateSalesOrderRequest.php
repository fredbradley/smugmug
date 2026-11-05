<?php

namespace FredBradley\SmugMug\Requests\Sales;

use FredBradley\SmugMug\Data\Commerce\SalesOrder;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\WriteRequest;
use FredBradley\SmugMug\Support\Path;
use JsonSerializable;
use Saloon\Enums\Method;

/**
 * Release, or change the delivery of, a sales order: PATCH /api/v2/sales/{nickname}/order/{orderId}.
 *
 * Parameters (in the body):
 *  - Action: ReleaseProDelay, PersonalDeliveryOff, BoutiquePackagingOn or BoutiquePackagingOff
 */
class UpdateSalesOrderRequest extends WriteRequest
{
    protected Method $method = Method::PATCH;

    protected ?string $dto = SalesOrder::class;

    /**
     * @param  array<mixed>|JsonSerializable  $body  SmugMug's field names and values, or a model.
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $nickname, public readonly string $orderId, array|JsonSerializable $body = [], Options|array $options = [])
    {
        $this->setBody($body, $options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/sales/'.Path::segment($this->nickname).'/order/'.Path::segment($this->orderId);
    }
}
