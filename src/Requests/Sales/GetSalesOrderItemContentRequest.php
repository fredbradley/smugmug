<?php

namespace FredBradley\SmugMug\Requests\Sales;

use FredBradley\SmugMug\Data\Commerce\SalesOrderItemContent;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ItemRequest;
use FredBradley\SmugMug\Support\Path;

/**
 * One image on a sales order: GET /api/v2/sales/{nickname}/order/{orderId}/content/{contentId}.
 */
class GetSalesOrderItemContentRequest extends ItemRequest
{
    protected ?string $dto = SalesOrderItemContent::class;

    /**
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $nickname, public readonly string $orderId, public readonly string $contentId, Options|array $options = [])
    {
        $this->setOptions($options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/sales/'.Path::segment($this->nickname).'/order/'.Path::segment($this->orderId).'/content/'.Path::segment($this->contentId);
    }
}
