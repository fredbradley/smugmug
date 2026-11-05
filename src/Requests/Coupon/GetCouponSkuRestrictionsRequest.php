<?php

namespace FredBradley\SmugMug\Requests\Coupon;

use FredBradley\SmugMug\Data\Commerce\CatalogSku;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ListRequest;
use FredBradley\SmugMug\Support\Path;

/**
 * The SKUs a coupon is restricted to: GET /api/v2/coupon/user/{nickname}/code/{couponCode}!catalogskurestrictions.
 */
class GetCouponSkuRestrictionsRequest extends ListRequest
{
    protected ?string $dto = CatalogSku::class;

    /**
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $nickname, public readonly string $couponCode, Options|array $options = [])
    {
        $this->setOptions($options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/coupon/user/'.Path::segment($this->nickname).'/code/'.Path::segment($this->couponCode).'!catalogskurestrictions';
    }
}
