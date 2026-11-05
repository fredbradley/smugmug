<?php

namespace FredBradley\SmugMug\Resources;

use FredBradley\SmugMug\Data\Album\Album;
use FredBradley\SmugMug\Data\Commerce\CatalogCategory;
use FredBradley\SmugMug\Data\Commerce\CatalogProduct;
use FredBradley\SmugMug\Data\Commerce\CatalogSku;
use FredBradley\SmugMug\Data\Commerce\Coupon;
use FredBradley\SmugMug\Data\Data;
use FredBradley\SmugMug\Data\Node\Node;
use FredBradley\SmugMug\Listing;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\Coupon\CreateCouponRequest;
use FredBradley\SmugMug\Requests\Coupon\GetCouponAlbumsRequest;
use FredBradley\SmugMug\Requests\Coupon\GetCouponCategoryRestrictionsRequest;
use FredBradley\SmugMug\Requests\Coupon\GetCouponNodesRequest;
use FredBradley\SmugMug\Requests\Coupon\GetCouponProductRestrictionsRequest;
use FredBradley\SmugMug\Requests\Coupon\GetCouponRequest;
use FredBradley\SmugMug\Requests\Coupon\GetCouponSkuRestrictionsRequest;
use FredBradley\SmugMug\Requests\Coupon\GetCouponSubCategoryRestrictionsRequest;
use FredBradley\SmugMug\Requests\Coupon\GetUserCouponsRequest;
use FredBradley\SmugMug\Requests\Coupon\UpdateCouponRequest;

/**
 * Coupons, from SmugMug::coupons().
 */
class CouponResource extends BaseResource
{
    /**
     * A user's coupons: GET /api/v2/user/{nickname}!coupons.
     *
     *  - SortBy: Name, Date Created, Coupon Code or Most Used
     *  - SortOrder: Ascending or Descending
     *  - ShowExpired
     *
     * @param  Options|array<string, mixed>  $options
     * @return Listing<Coupon>
     */
    public function forUser(string $nickname, Options|array $options = []): Listing
    {
        return $this->listing(fn (Options $o) => new GetUserCouponsRequest($nickname, $o), $options);
    }

    /**
     * Create a coupon: POST /api/v2/user/{nickname}!coupons.
     *
     *  - CouponCode (required), Title (required), Currency (required), ValidFrom (required), DiscountType (required) (amount, percent, cost or shipping)
     *  - DiscountAmount, MaxDiscount, MinPurchase, MaxItems, MaxUses
     *  - ValidThru
     *  - Shipping, InternationalShipping, ShowBanner
     *  - Description
     *  - Nodes, CatalogRestrictions: what it applies to
     *
     * @param  array<string, mixed>|Data  $fields
     * @param  Options|array<string, mixed>  $options
     */
    public function create(string $nickname, array|Data $fields, Options|array $options = []): ?Coupon
    {
        return $this->fetch(new CreateCouponRequest($nickname, $fields, $this->options($options)));
    }

    /**
     * A coupon, by code: GET /api/v2/coupon/user/{nickname}/code/{couponCode}.
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function get(string $nickname, string $couponCode, Options|array $options = []): ?Coupon
    {
        return $this->fetch(new GetCouponRequest($nickname, $couponCode, $this->options($options)));
    }

    /**
     * Change a coupon: PATCH /api/v2/coupon/user/{nickname}/code/{couponCode}.
     *
     *  - any of the fields a coupon is created with
     *
     * @param  array<string, mixed>|Data  $fields
     * @param  Options|array<string, mixed>  $options
     */
    public function update(string $nickname, string $couponCode, array|Data $fields, Options|array $options = []): ?Coupon
    {
        return $this->fetch(new UpdateCouponRequest($nickname, $couponCode, $fields, $this->options($options)));
    }

    /**
     * The albums a coupon applies to: GET /api/v2/coupon/user/{nickname}/code/{couponCode}!albums.
     *
     * @param  Options|array<string, mixed>  $options
     * @return Listing<Album>
     */
    public function albums(string $nickname, string $couponCode, Options|array $options = []): Listing
    {
        return $this->listing(fn (Options $o) => new GetCouponAlbumsRequest($nickname, $couponCode, $o), $options);
    }

    /**
     * The nodes a coupon applies to: GET /api/v2/coupon/user/{nickname}/code/{couponCode}!nodes.
     *
     * @param  Options|array<string, mixed>  $options
     * @return Listing<Node>
     */
    public function nodes(string $nickname, string $couponCode, Options|array $options = []): Listing
    {
        return $this->listing(fn (Options $o) => new GetCouponNodesRequest($nickname, $couponCode, $o), $options);
    }

    /**
     * The categories a coupon is restricted to: GET /api/v2/coupon/user/{nickname}/code/{couponCode}!catalogcategoryrestrictions.
     *
     * @param  Options|array<string, mixed>  $options
     * @return Listing<CatalogCategory>
     */
    public function categoryRestrictions(string $nickname, string $couponCode, Options|array $options = []): Listing
    {
        return $this->listing(fn (Options $o) => new GetCouponCategoryRestrictionsRequest($nickname, $couponCode, $o), $options);
    }

    /**
     * The subcategories a coupon is restricted to: GET /api/v2/coupon/user/{nickname}/code/{couponCode}!catalogsubcategoryrestrictions.
     *
     * @param  Options|array<string, mixed>  $options
     * @return Listing<CatalogCategory>
     */
    public function subCategoryRestrictions(string $nickname, string $couponCode, Options|array $options = []): Listing
    {
        return $this->listing(fn (Options $o) => new GetCouponSubCategoryRestrictionsRequest($nickname, $couponCode, $o), $options);
    }

    /**
     * The products a coupon is restricted to: GET /api/v2/coupon/user/{nickname}/code/{couponCode}!catalogproductrestrictions.
     *
     * @param  Options|array<string, mixed>  $options
     * @return Listing<CatalogProduct>
     */
    public function productRestrictions(string $nickname, string $couponCode, Options|array $options = []): Listing
    {
        return $this->listing(fn (Options $o) => new GetCouponProductRestrictionsRequest($nickname, $couponCode, $o), $options);
    }

    /**
     * The SKUs a coupon is restricted to: GET /api/v2/coupon/user/{nickname}/code/{couponCode}!catalogskurestrictions.
     *
     * @param  Options|array<string, mixed>  $options
     * @return Listing<CatalogSku>
     */
    public function skuRestrictions(string $nickname, string $couponCode, Options|array $options = []): Listing
    {
        return $this->listing(fn (Options $o) => new GetCouponSkuRestrictionsRequest($nickname, $couponCode, $o), $options);
    }
}
