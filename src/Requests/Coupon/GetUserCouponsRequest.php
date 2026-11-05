<?php

namespace FredBradley\SmugMug\Requests\Coupon;

use FredBradley\SmugMug\Data\Commerce\Coupon;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ListRequest;
use FredBradley\SmugMug\Support\Path;

/**
 * A user's coupons: GET /api/v2/user/{nickname}!coupons.
 *
 * Parameters (in the options):
 *  - SortBy: Name, Date Created, Coupon Code or Most Used
 *  - SortOrder: Ascending or Descending
 *  - ShowExpired
 */
class GetUserCouponsRequest extends ListRequest
{
    protected ?string $dto = Coupon::class;

    /**
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $nickname, Options|array $options = [])
    {
        $this->setOptions($options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/user/'.Path::segment($this->nickname).'!coupons';
    }
}
