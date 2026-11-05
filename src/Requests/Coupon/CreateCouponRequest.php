<?php

namespace FredBradley\SmugMug\Requests\Coupon;

use FredBradley\SmugMug\Data\Commerce\Coupon;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\WriteRequest;
use FredBradley\SmugMug\Support\Path;
use JsonSerializable;

/**
 * Create a coupon: POST /api/v2/user/{nickname}!coupons.
 *
 * Parameters (in the body):
 *  - CouponCode (required), Title (required), Currency (required), ValidFrom (required), DiscountType (required) (amount, percent, cost or shipping)
 *  - DiscountAmount, MaxDiscount, MinPurchase, MaxItems, MaxUses
 *  - ValidThru
 *  - Shipping, InternationalShipping, ShowBanner
 *  - Description
 *  - Nodes, CatalogRestrictions: what it applies to
 */
class CreateCouponRequest extends WriteRequest
{
    protected ?string $dto = Coupon::class;

    /**
     * @param  array<mixed>|JsonSerializable  $body  SmugMug's field names and values, or a model.
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $nickname, array|JsonSerializable $body = [], Options|array $options = [])
    {
        $this->setBody($body, $options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/user/'.Path::segment($this->nickname).'!coupons';
    }
}
