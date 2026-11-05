<?php

namespace FredBradley\SmugMug\Resources;

use FredBradley\SmugMug\Data\Commerce\Address;
use FredBradley\SmugMug\Data\Commerce\CatalogSku;
use FredBradley\SmugMug\Data\Commerce\Order;
use FredBradley\SmugMug\Data\Commerce\OrderItem;
use FredBradley\SmugMug\Data\Commerce\OrderItemContent;
use FredBradley\SmugMug\Data\Commerce\OrderVendor;
use FredBradley\SmugMug\Data\Commerce\Shipment;
use FredBradley\SmugMug\Data\Commerce\TrackingNumbers;
use FredBradley\SmugMug\Data\Image\Image;
use FredBradley\SmugMug\Listing;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\Order\AddOrderToCartRequest;
use FredBradley\SmugMug\Requests\Order\GetOrderBillingAddressRequest;
use FredBradley\SmugMug\Requests\Order\GetOrderItemContentRequest;
use FredBradley\SmugMug\Requests\Order\GetOrderItemContentsRequest;
use FredBradley\SmugMug\Requests\Order\GetOrderItemContentTypeRequest;
use FredBradley\SmugMug\Requests\Order\GetOrderItemParentRequest;
use FredBradley\SmugMug\Requests\Order\GetOrderItemRequest;
use FredBradley\SmugMug\Requests\Order\GetOrderItemsRequest;
use FredBradley\SmugMug\Requests\Order\GetOrderItemTypeRequest;
use FredBradley\SmugMug\Requests\Order\GetOrderRequest;
use FredBradley\SmugMug\Requests\Order\GetOrderShipmentItemsRequest;
use FredBradley\SmugMug\Requests\Order\GetOrderShipmentRequest;
use FredBradley\SmugMug\Requests\Order\GetOrderShipmentsRequest;
use FredBradley\SmugMug\Requests\Order\GetOrderShipmentTrackingNumbersRequest;
use FredBradley\SmugMug\Requests\Order\GetOrderShippingAddressRequest;
use FredBradley\SmugMug\Requests\Order\GetOrderVendorShipmentsRequest;
use FredBradley\SmugMug\Requests\Order\GetOrderVendorsRequest;
use FredBradley\SmugMug\Requests\Order\GetUserOrdersRequest;

/**
 * Orders the user placed as a buyer, from SmugMug::orders().
 */
class OrderResource extends BaseResource
{
    /**
     * The orders a user placed: GET /api/v2/user/{nickname}!orders.
     *
     * @param  Options|array<string, mixed>  $options
     * @return Listing<Order>
     */
    public function forUser(string $nickname, Options|array $options = []): Listing
    {
        return $this->listing(fn (Options $o) => new GetUserOrdersRequest($nickname, $o), $options);
    }

    /**
     * An order, by id: GET /api/v2/order/{orderId}.
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function get(string $orderId, Options|array $options = []): ?Order
    {
        return $this->fetch(new GetOrderRequest($orderId, $this->options($options)));
    }

    /**
     * The lines of an order: GET /api/v2/order/{orderId}!items.
     *
     * @param  Options|array<string, mixed>  $options
     * @return Listing<OrderItem>
     */
    public function items(string $orderId, Options|array $options = []): Listing
    {
        return $this->listing(fn (Options $o) => new GetOrderItemsRequest($orderId, $o), $options);
    }

    /**
     * One line of an order: GET /api/v2/order/{orderId}/item/{itemId}.
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function item(string $orderId, string $itemId, Options|array $options = []): ?OrderItem
    {
        return $this->fetch(new GetOrderItemRequest($orderId, $itemId, $this->options($options)));
    }

    /**
     * The images on a line of an order: GET /api/v2/order/{orderId}/item/{itemId}!contents.
     *
     * @param  Options|array<string, mixed>  $options
     * @return Listing<OrderItemContent>
     */
    public function itemContents(string $orderId, string $itemId, Options|array $options = []): Listing
    {
        return $this->listing(fn (Options $o) => new GetOrderItemContentsRequest($orderId, $itemId, $o), $options);
    }

    /**
     * The order a line belongs to: GET /api/v2/order/{orderId}/item/{itemId}!parent.
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function itemParent(string $orderId, string $itemId, Options|array $options = []): ?Order
    {
        return $this->fetch(new GetOrderItemParentRequest($orderId, $itemId, $this->options($options)));
    }

    /**
     * The SKU a line is for: GET /api/v2/order/{orderId}/item/{itemId}!type.
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function itemType(string $orderId, string $itemId, Options|array $options = []): ?CatalogSku
    {
        return $this->fetch(new GetOrderItemTypeRequest($orderId, $itemId, $this->options($options)));
    }

    /**
     * One image on an order: GET /api/v2/order/{orderId}/content/{contentId}.
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function content(string $orderId, string $contentId, Options|array $options = []): ?OrderItemContent
    {
        return $this->fetch(new GetOrderItemContentRequest($orderId, $contentId, $this->options($options)));
    }

    /**
     * The image an order's content is: GET /api/v2/order/{orderId}/content/{contentId}!type.
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function contentType(string $orderId, string $contentId, Options|array $options = []): ?Image
    {
        return $this->fetch(new GetOrderItemContentTypeRequest($orderId, $contentId, $this->options($options)));
    }

    /**
     * The shipments of an order: GET /api/v2/order/{orderId}!shipments.
     *
     * @param  Options|array<string, mixed>  $options
     * @return Listing<Shipment>
     */
    public function shipments(string $orderId, Options|array $options = []): Listing
    {
        return $this->listing(fn (Options $o) => new GetOrderShipmentsRequest($orderId, $o), $options);
    }

    /**
     * One shipment of an order: GET /api/v2/order/{orderId}/shipment/{shipmentId}.
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function shipment(string $orderId, string $shipmentId, Options|array $options = []): ?Shipment
    {
        return $this->fetch(new GetOrderShipmentRequest($orderId, $shipmentId, $this->options($options)));
    }

    /**
     * The lines in a shipment: GET /api/v2/order/{orderId}/shipment/{shipmentId}!items.
     *
     * @param  Options|array<string, mixed>  $options
     * @return Listing<OrderItem>
     */
    public function shipmentItems(string $orderId, string $shipmentId, Options|array $options = []): Listing
    {
        return $this->listing(fn (Options $o) => new GetOrderShipmentItemsRequest($orderId, $shipmentId, $o), $options);
    }

    /**
     * The tracking numbers of a shipment: GET /api/v2/order/{orderId}/shipment/{shipmentId}!trackingnumbers.
     *
     * @param  Options|array<string, mixed>  $options
     * @return Listing<TrackingNumbers>
     */
    public function trackingNumbers(string $orderId, string $shipmentId, Options|array $options = []): Listing
    {
        return $this->listing(fn (Options $o) => new GetOrderShipmentTrackingNumbersRequest($orderId, $shipmentId, $o), $options);
    }

    /**
     * An order's billing address: GET /api/v2/order/{orderId}!billingaddress.
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function billingAddress(string $orderId, Options|array $options = []): ?Address
    {
        return $this->fetch(new GetOrderBillingAddressRequest($orderId, $this->options($options)));
    }

    /**
     * An order's shipping address: GET /api/v2/order/{orderId}!shippingaddress.
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function shippingAddress(string $orderId, Options|array $options = []): ?Address
    {
        return $this->fetch(new GetOrderShippingAddressRequest($orderId, $this->options($options)));
    }

    /**
     * The vendors fulfilling an order: GET /api/v2/order/{orderId}!vendors.
     *
     * @param  Options|array<string, mixed>  $options
     * @return Listing<OrderVendor>
     */
    public function vendors(string $orderId, Options|array $options = []): Listing
    {
        return $this->listing(fn (Options $o) => new GetOrderVendorsRequest($orderId, $o), $options);
    }

    /**
     * The shipments from one vendor of an order: GET /api/v2/order/{orderId}/vendor/{vendor}!shipments.
     *
     * @param  Options|array<string, mixed>  $options
     * @return Listing<Shipment>
     */
    public function vendorShipments(string $orderId, string $vendor, Options|array $options = []): Listing
    {
        return $this->listing(fn (Options $o) => new GetOrderVendorShipmentsRequest($orderId, $vendor, $o), $options);
    }

    /**
     * Add an order's items to the cart again: POST /api/v2/order/{orderId}!addtocart.
     *
     * @param  array<string, mixed>  $parameters
     * @param  Options|array<string, mixed>  $options
     * @return array<string, mixed>
     */
    public function addToCart(string $orderId, array $parameters = [], Options|array $options = []): array
    {
        return $this->fetch(new AddOrderToCartRequest($orderId, $parameters, $this->options($options)));
    }
}
