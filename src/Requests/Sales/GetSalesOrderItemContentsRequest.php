<?php

namespace FredBradley\SmugMug\Requests\Sales;

use FredBradley\SmugMug\Data\Commerce\SalesOrderItemContent;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ListRequest;
use FredBradley\SmugMug\Support\Path;

/**
 * The images on a line of a sales order: GET /api/v2/sales/{nickname}/order/{orderId}/item/{itemId}!contents.
 */
class GetSalesOrderItemContentsRequest extends ListRequest
{
    protected ?string $dto = SalesOrderItemContent::class;

    /**
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $nickname, public readonly string $orderId, public readonly string $itemId, Options|array $options = [])
    {
        $this->setOptions($options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/sales/'.Path::segment($this->nickname).'/order/'.Path::segment($this->orderId).'/item/'.Path::segment($this->itemId).'!contents';
    }
}
