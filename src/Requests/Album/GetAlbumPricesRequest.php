<?php

namespace FredBradley\SmugMug\Requests\Album;

use FredBradley\SmugMug\Data\Commerce\CatalogSkuPrice;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ListRequest;
use FredBradley\SmugMug\Support\Path;

/**
 * What an album's images sell for: GET /api/v2/album/{albumKey}!prices.
 *
 * Parameters (in the options):
 *  - Currency: USD, CAD, GBP, EUR, AUD, JPY, CHF, NZD, SEK or HKD
 */
class GetAlbumPricesRequest extends ListRequest
{
    protected ?string $dto = CatalogSkuPrice::class;

    /**
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $albumKey, Options|array $options = [])
    {
        $this->setOptions($options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/album/'.Path::segment($this->albumKey).'!prices';
    }
}
