<?php

namespace FredBradley\SmugMug\Resources;

use FredBradley\SmugMug\Data\Commerce\Pricelist;
use FredBradley\SmugMug\Data\Commerce\PricelistSkuPrice;
use FredBradley\SmugMug\Data\Data;
use FredBradley\SmugMug\Listing;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\Pricelist\GetPricelistPricesRequest;
use FredBradley\SmugMug\Requests\Pricelist\GetPricelistRequest;
use FredBradley\SmugMug\Requests\Pricelist\GetPricelistSkuPriceRequest;
use FredBradley\SmugMug\Requests\Pricelist\UpdatePricelistRequest;

/**
 * Pricelists, from SmugMug::pricelists().
 */
class PricelistResource extends BaseResource
{
    /**
     * A pricelist, by key: GET /api/v2/pricelist/{pricelistKey}.
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function get(string $pricelistKey, Options|array $options = []): ?Pricelist
    {
        return $this->fetch(new GetPricelistRequest($pricelistKey, $this->options($options)));
    }

    /**
     * Change a pricelist: PATCH /api/v2/pricelist/{pricelistKey}.
     *
     *  - Name, ColorCorrection, Default, Currency
     *
     * @param  array<string, mixed>|Data  $fields
     * @param  Options|array<string, mixed>  $options
     */
    public function update(string $pricelistKey, array|Data $fields, Options|array $options = []): ?Pricelist
    {
        return $this->fetch(new UpdatePricelistRequest($pricelistKey, $fields, $this->options($options)));
    }

    /**
     * The prices on a pricelist: GET /api/v2/pricelist/{pricelistKey}!prices.
     *
     *  - Currency
     *
     * @param  Options|array<string, mixed>  $options
     * @return Listing<PricelistSkuPrice>
     */
    public function prices(string $pricelistKey, Options|array $options = []): Listing
    {
        return $this->listing(fn (Options $o) => new GetPricelistPricesRequest($pricelistKey, $o), $options);
    }

    /**
     * The price of one SKU on a pricelist: GET /api/v2/pricelist/{pricelistKey}/skuprice/{skuId}.
     *
     *  - Currency
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function price(string $pricelistKey, string $skuId, Options|array $options = []): ?PricelistSkuPrice
    {
        return $this->fetch(new GetPricelistSkuPriceRequest($pricelistKey, $skuId, $this->options($options)));
    }
}
