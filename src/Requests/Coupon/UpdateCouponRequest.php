<?php

namespace FredBradley\SmugMug\Requests\Coupon;

use FredBradley\SmugMug\Data\Commerce\Coupon;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\WriteRequest;
use FredBradley\SmugMug\Support\Path;
use JsonSerializable;
use Saloon\Enums\Method;

/**
 * Change a coupon: PATCH /api/v2/coupon/user/{nickname}/code/{couponCode}.
 *
 * Parameters (in the body):
 *  - any of the fields a coupon is created with
 */
class UpdateCouponRequest extends WriteRequest
{
    protected Method $method = Method::PATCH;

    protected ?string $dto = Coupon::class;

    /**
     * @param  array<mixed>|JsonSerializable  $body  SmugMug's field names and values, or a model.
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $nickname, public readonly string $couponCode, array|JsonSerializable $body = [], Options|array $options = [])
    {
        $this->setBody($body, $options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/coupon/user/'.Path::segment($this->nickname).'/code/'.Path::segment($this->couponCode);
    }
}
