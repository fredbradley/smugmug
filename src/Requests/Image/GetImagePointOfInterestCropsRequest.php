<?php

namespace FredBradley\SmugMug\Requests\Image;

use FredBradley\SmugMug\Data\Image\PointOfInterestCrops;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ListRequest;
use FredBradley\SmugMug\Support\Path;

/**
 * The crops of an image around its point of interest: GET /api/v2/image/{imageKey}!poicrops.
 */
class GetImagePointOfInterestCropsRequest extends ListRequest
{
    protected ?string $dto = PointOfInterestCrops::class;

    /**
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $imageKey, Options|array $options = [])
    {
        $this->setOptions($options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/image/'.Path::segment($this->imageKey).'!poicrops';
    }
}
