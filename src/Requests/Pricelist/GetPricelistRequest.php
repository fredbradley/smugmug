<?php

namespace FredBradley\SmugMug\Requests\Pricelist;

use FredBradley\SmugMug\Data\Commerce\Pricelist;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ItemRequest;
use FredBradley\SmugMug\Support\Path;

/**
 * A pricelist, by key: GET /api/v2/pricelist/{pricelistKey}.
 */
class GetPricelistRequest extends ItemRequest
{
    protected ?string $dto = Pricelist::class;

    /**
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $pricelistKey, Options|array $options = [])
    {
        $this->setOptions($options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/pricelist/'.Path::segment($this->pricelistKey);
    }
}
