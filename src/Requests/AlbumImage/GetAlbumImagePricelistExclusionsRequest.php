<?php

namespace FredBradley\SmugMug\Requests\AlbumImage;

use FredBradley\SmugMug\Data\Image\PricelistExclusions;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ItemRequest;
use FredBradley\SmugMug\Support\Path;

/**
 * The pricelist products that may not be bought for an album image: GET /api/v2/album/{albumKey}/image/{imageKey}!pricelistexclusions.
 */
class GetAlbumImagePricelistExclusionsRequest extends ItemRequest
{
    protected ?string $dto = PricelistExclusions::class;

    /**
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $albumKey, public readonly string $imageKey, Options|array $options = [])
    {
        $this->setOptions($options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/album/'.Path::segment($this->albumKey).'/image/'.Path::segment($this->imageKey).'!pricelistexclusions';
    }
}
