<?php

namespace FredBradley\SmugMug\Requests\Sales;

use FredBradley\SmugMug\Data\Commerce\CatalogSku;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ItemRequest;
use FredBradley\SmugMug\Support\Path;

/**
 * The SKU a line is for: GET /api/v2/sales/{nickname}/order/{orderId}/item/{itemId}!type.
 */
class GetSalesOrderItemTypeRequest extends ItemRequest
{
    protected ?string $dto = CatalogSku::class;

    /**
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $nickname, public readonly string $orderId, public readonly string $itemId, Options|array $options = [])
    {
        $this->setOptions($options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/sales/'.Path::segment($this->nickname).'/order/'.Path::segment($this->orderId).'/item/'.Path::segment($this->itemId).'!type';
    }
}
