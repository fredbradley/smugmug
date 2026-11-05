<?php

namespace FredBradley\SmugMug\Requests\Sales;

use FredBradley\SmugMug\Data\Commerce\SalesOrderItem;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\WriteRequest;
use FredBradley\SmugMug\Support\Path;
use JsonSerializable;
use Saloon\Enums\Method;

/**
 * Turn colour correction on or off for a line: PATCH /api/v2/sales/{nickname}/order/{orderId}/item/{itemId}.
 *
 * Parameters (in the body):
 *  - Action: ColorCorrectionOn or ColorCorrectionOff
 */
class UpdateSalesOrderItemRequest extends WriteRequest
{
    protected Method $method = Method::PATCH;

    protected ?string $dto = SalesOrderItem::class;

    /**
     * @param  array<mixed>|JsonSerializable  $body  SmugMug's field names and values, or a model.
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $nickname, public readonly string $orderId, public readonly string $itemId, array|JsonSerializable $body = [], Options|array $options = [])
    {
        $this->setBody($body, $options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/sales/'.Path::segment($this->nickname).'/order/'.Path::segment($this->orderId).'/item/'.Path::segment($this->itemId);
    }
}
