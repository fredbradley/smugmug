<?php

namespace FredBradley\SmugMug\Requests\Sales;

use FredBradley\SmugMug\Data\Commerce\SalesOrderItemContent;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\WriteRequest;
use FredBradley\SmugMug\Support\Path;
use JsonSerializable;
use Saloon\Enums\Method;

/**
 * Change how an image on a sales order is cropped: PATCH /api/v2/sales/{nickname}/order/{orderId}/content/{contentId}.
 *
 * Parameters (in the body):
 *  - Action: AdjustCrop, DisableCrop, DisablePrintmark or ResetCrop
 *  - CropLeft, CropTop, CropWidth, CropHeight, CropRotation, CropAcknowledged: the crop for AdjustCrop
 */
class UpdateSalesOrderItemContentRequest extends WriteRequest
{
    protected Method $method = Method::PATCH;

    protected ?string $dto = SalesOrderItemContent::class;

    /**
     * @param  array<mixed>|JsonSerializable  $body  SmugMug's field names and values, or a model.
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $nickname, public readonly string $orderId, public readonly string $contentId, array|JsonSerializable $body = [], Options|array $options = [])
    {
        $this->setBody($body, $options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/sales/'.Path::segment($this->nickname).'/order/'.Path::segment($this->orderId).'/content/'.Path::segment($this->contentId);
    }
}
