<?php

namespace FredBradley\SmugMug\Resources;

use FredBradley\SmugMug\Data\Commerce\Address;
use FredBradley\SmugMug\Data\Commerce\CatalogSku;
use FredBradley\SmugMug\Data\Commerce\ProPayment;
use FredBradley\SmugMug\Data\Commerce\ProTaxPayment;
use FredBradley\SmugMug\Data\Commerce\SalesOrder;
use FredBradley\SmugMug\Data\Commerce\SalesOrderItem;
use FredBradley\SmugMug\Data\Commerce\SalesOrderItemContent;
use FredBradley\SmugMug\Data\Commerce\Shipment;
use FredBradley\SmugMug\Data\Data;
use FredBradley\SmugMug\Data\Image\Image;
use FredBradley\SmugMug\Listing;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\Sales\GetSalesOrderBillingAddressRequest;
use FredBradley\SmugMug\Requests\Sales\GetSalesOrderItemContentRequest;
use FredBradley\SmugMug\Requests\Sales\GetSalesOrderItemContentsRequest;
use FredBradley\SmugMug\Requests\Sales\GetSalesOrderItemContentTypeRequest;
use FredBradley\SmugMug\Requests\Sales\GetSalesOrderItemParentRequest;
use FredBradley\SmugMug\Requests\Sales\GetSalesOrderItemRequest;
use FredBradley\SmugMug\Requests\Sales\GetSalesOrderItemsRequest;
use FredBradley\SmugMug\Requests\Sales\GetSalesOrderItemTypeRequest;
use FredBradley\SmugMug\Requests\Sales\GetSalesOrderProSalePaymentsRequest;
use FredBradley\SmugMug\Requests\Sales\GetSalesOrderProTaxPaymentsRequest;
use FredBradley\SmugMug\Requests\Sales\GetSalesOrderRequest;
use FredBradley\SmugMug\Requests\Sales\GetSalesOrderShipmentRequest;
use FredBradley\SmugMug\Requests\Sales\GetSalesOrderShipmentsRequest;
use FredBradley\SmugMug\Requests\Sales\GetSalesOrderShippingAddressRequest;
use FredBradley\SmugMug\Requests\Sales\GetUserProPaymentsRequest;
use FredBradley\SmugMug\Requests\Sales\GetUserSalesOrdersRequest;
use FredBradley\SmugMug\Requests\Sales\UpdateSalesOrderItemContentRequest;
use FredBradley\SmugMug\Requests\Sales\UpdateSalesOrderItemRequest;
use FredBradley\SmugMug\Requests\Sales\UpdateSalesOrderRequest;

/**
 * Orders buyers placed with the user, and the payments for them, from SmugMug::sales().
 */
class SalesResource extends BaseResource
{
    /**
     * The orders buyers placed with a user: GET /api/v2/user/{nickname}!salesorders.
     *
     * @param  Options|array<string, mixed>  $options
     * @return Listing<SalesOrder>
     */
    public function forUser(string $nickname, Options|array $options = []): Listing
    {
        return $this->listing(fn (Options $o) => new GetUserSalesOrdersRequest($nickname, $o), $options);
    }

    /**
     * The payments SmugMug made to a user for their sales: GET /api/v2/user/{nickname}!propayments.
     *
     * @param  Options|array<string, mixed>  $options
     * @return Listing<ProPayment>
     */
    public function proPayments(string $nickname, Options|array $options = []): Listing
    {
        return $this->listing(fn (Options $o) => new GetUserProPaymentsRequest($nickname, $o), $options);
    }

    /**
     * A sales order, by id: GET /api/v2/sales/{nickname}/order/{orderId}.
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function get(string $nickname, string $orderId, Options|array $options = []): ?SalesOrder
    {
        return $this->fetch(new GetSalesOrderRequest($nickname, $orderId, $this->options($options)));
    }

    /**
     * Release, or change the delivery of, a sales order: PATCH /api/v2/sales/{nickname}/order/{orderId}.
     *
     *  - Action: ReleaseProDelay, PersonalDeliveryOff, BoutiquePackagingOn or BoutiquePackagingOff
     *
     * @param  array<string, mixed>|Data  $fields
     * @param  Options|array<string, mixed>  $options
     */
    public function update(string $nickname, string $orderId, array|Data $fields, Options|array $options = []): ?SalesOrder
    {
        return $this->fetch(new UpdateSalesOrderRequest($nickname, $orderId, $fields, $this->options($options)));
    }

    /**
     * The lines of a sales order: GET /api/v2/sales/{nickname}/order/{orderId}!items.
     *
     * @param  Options|array<string, mixed>  $options
     * @return Listing<SalesOrderItem>
     */
    public function items(string $nickname, string $orderId, Options|array $options = []): Listing
    {
        return $this->listing(fn (Options $o) => new GetSalesOrderItemsRequest($nickname, $orderId, $o), $options);
    }

    /**
     * One line of a sales order: GET /api/v2/sales/{nickname}/order/{orderId}/item/{itemId}.
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function item(string $nickname, string $orderId, string $itemId, Options|array $options = []): ?SalesOrderItem
    {
        return $this->fetch(new GetSalesOrderItemRequest($nickname, $orderId, $itemId, $this->options($options)));
    }

    /**
     * Turn colour correction on or off for a line: PATCH /api/v2/sales/{nickname}/order/{orderId}/item/{itemId}.
     *
     *  - Action: ColorCorrectionOn or ColorCorrectionOff
     *
     * @param  array<string, mixed>|Data  $fields
     * @param  Options|array<string, mixed>  $options
     */
    public function updateItem(string $nickname, string $orderId, string $itemId, array|Data $fields, Options|array $options = []): ?SalesOrderItem
    {
        return $this->fetch(new UpdateSalesOrderItemRequest($nickname, $orderId, $itemId, $fields, $this->options($options)));
    }

    /**
     * The images on a line of a sales order: GET /api/v2/sales/{nickname}/order/{orderId}/item/{itemId}!contents.
     *
     * @param  Options|array<string, mixed>  $options
     * @return Listing<SalesOrderItemContent>
     */
    public function itemContents(string $nickname, string $orderId, string $itemId, Options|array $options = []): Listing
    {
        return $this->listing(fn (Options $o) => new GetSalesOrderItemContentsRequest($nickname, $orderId, $itemId, $o), $options);
    }

    /**
     * The sales order a line belongs to: GET /api/v2/sales/{nickname}/order/{orderId}/item/{itemId}!parent.
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function itemParent(string $nickname, string $orderId, string $itemId, Options|array $options = []): ?SalesOrder
    {
        return $this->fetch(new GetSalesOrderItemParentRequest($nickname, $orderId, $itemId, $this->options($options)));
    }

    /**
     * The SKU a line is for: GET /api/v2/sales/{nickname}/order/{orderId}/item/{itemId}!type.
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function itemType(string $nickname, string $orderId, string $itemId, Options|array $options = []): ?CatalogSku
    {
        return $this->fetch(new GetSalesOrderItemTypeRequest($nickname, $orderId, $itemId, $this->options($options)));
    }

    /**
     * One image on a sales order: GET /api/v2/sales/{nickname}/order/{orderId}/content/{contentId}.
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function content(string $nickname, string $orderId, string $contentId, Options|array $options = []): ?SalesOrderItemContent
    {
        return $this->fetch(new GetSalesOrderItemContentRequest($nickname, $orderId, $contentId, $this->options($options)));
    }

    /**
     * Change how an image on a sales order is cropped: PATCH /api/v2/sales/{nickname}/order/{orderId}/content/{contentId}.
     *
     *  - Action: AdjustCrop, DisableCrop, DisablePrintmark or ResetCrop
     *  - CropLeft, CropTop, CropWidth, CropHeight, CropRotation, CropAcknowledged: the crop for AdjustCrop
     *
     * @param  array<string, mixed>|Data  $fields
     * @param  Options|array<string, mixed>  $options
     */
    public function updateContent(string $nickname, string $orderId, string $contentId, array|Data $fields, Options|array $options = []): ?SalesOrderItemContent
    {
        return $this->fetch(new UpdateSalesOrderItemContentRequest($nickname, $orderId, $contentId, $fields, $this->options($options)));
    }

    /**
     * The image a sales order's content is: GET /api/v2/sales/{nickname}/order/{orderId}/content/{contentId}!type.
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function contentType(string $nickname, string $orderId, string $contentId, Options|array $options = []): ?Image
    {
        return $this->fetch(new GetSalesOrderItemContentTypeRequest($nickname, $orderId, $contentId, $this->options($options)));
    }

    /**
     * The shipments of a sales order: GET /api/v2/sales/{nickname}/order/{orderId}!shipments.
     *
     * @param  Options|array<string, mixed>  $options
     * @return Listing<Shipment>
     */
    public function shipments(string $nickname, string $orderId, Options|array $options = []): Listing
    {
        return $this->listing(fn (Options $o) => new GetSalesOrderShipmentsRequest($nickname, $orderId, $o), $options);
    }

    /**
     * One shipment of a sales order: GET /api/v2/sales/{nickname}/order/{orderId}/shipment/{shipmentId}.
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function shipment(string $nickname, string $orderId, string $shipmentId, Options|array $options = []): ?Shipment
    {
        return $this->fetch(new GetSalesOrderShipmentRequest($nickname, $orderId, $shipmentId, $this->options($options)));
    }

    /**
     * A sales order's billing address: GET /api/v2/sales/{nickname}/order/{orderId}!billingaddress.
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function billingAddress(string $nickname, string $orderId, Options|array $options = []): ?Address
    {
        return $this->fetch(new GetSalesOrderBillingAddressRequest($nickname, $orderId, $this->options($options)));
    }

    /**
     * A sales order's shipping address: GET /api/v2/sales/{nickname}/order/{orderId}!shippingaddress.
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function shippingAddress(string $nickname, string $orderId, Options|array $options = []): ?Address
    {
        return $this->fetch(new GetSalesOrderShippingAddressRequest($nickname, $orderId, $this->options($options)));
    }

    /**
     * The payments to the user for a sales order: GET /api/v2/sales/{nickname}/order/{orderId}!prosalepayments.
     *
     * @param  Options|array<string, mixed>  $options
     * @return Listing<ProPayment>
     */
    public function proSalePayments(string $nickname, string $orderId, Options|array $options = []): Listing
    {
        return $this->listing(fn (Options $o) => new GetSalesOrderProSalePaymentsRequest($nickname, $orderId, $o), $options);
    }

    /**
     * The tax paid to the user for a sales order: GET /api/v2/sales/{nickname}/order/{orderId}!protaxpayments.
     *
     * @param  Options|array<string, mixed>  $options
     * @return Listing<ProTaxPayment>
     */
    public function proTaxPayments(string $nickname, string $orderId, Options|array $options = []): Listing
    {
        return $this->listing(fn (Options $o) => new GetSalesOrderProTaxPaymentsRequest($nickname, $orderId, $o), $options);
    }
}
