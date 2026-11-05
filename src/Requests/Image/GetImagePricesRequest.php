<?php

namespace FredBradley\SmugMug\Requests\Image;

use FredBradley\SmugMug\Data\Commerce\CatalogSkuPrice;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ListRequest;
use FredBradley\SmugMug\Support\Path;

/**
 * What an image sells for: GET /api/v2/image/{imageKey}!prices.
 *
 * Parameters (in the options):
 *  - Currency: USD, CAD, GBP, EUR, AUD, JPY, CHF, NZD, SEK or HKD
 */
class GetImagePricesRequest extends ListRequest
{
    protected ?string $dto = CatalogSkuPrice::class;

    /**
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $imageKey, Options|array $options = [])
    {
        $this->setOptions($options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/image/'.Path::segment($this->imageKey).'!prices';
    }
}
