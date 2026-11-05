<?php

namespace FredBradley\SmugMug\Requests\Image;

use FredBradley\SmugMug\Data\Image\PricelistExclusions;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ItemRequest;
use FredBradley\SmugMug\Support\Path;

/**
 * The pricelist products that may not be bought for an image: GET /api/v2/image/{imageKey}!pricelistexclusions.
 */
class GetImagePricelistExclusionsRequest extends ItemRequest
{
    protected ?string $dto = PricelistExclusions::class;

    /**
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $imageKey, Options|array $options = [])
    {
        $this->setOptions($options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/image/'.Path::segment($this->imageKey).'!pricelistexclusions';
    }
}
