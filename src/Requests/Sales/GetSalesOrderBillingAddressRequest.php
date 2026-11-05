<?php

namespace FredBradley\SmugMug\Requests\Sales;

use FredBradley\SmugMug\Data\Commerce\Address;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ItemRequest;
use FredBradley\SmugMug\Support\Path;

/**
 * A sales order's billing address: GET /api/v2/sales/{nickname}/order/{orderId}!billingaddress.
 */
class GetSalesOrderBillingAddressRequest extends ItemRequest
{
    protected ?string $dto = Address::class;

    /**
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $nickname, public readonly string $orderId, Options|array $options = [])
    {
        $this->setOptions($options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/sales/'.Path::segment($this->nickname).'/order/'.Path::segment($this->orderId).'!billingaddress';
    }
}
