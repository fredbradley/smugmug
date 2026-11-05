<?php

namespace FredBradley\SmugMug\Requests\Image;

use FredBradley\SmugMug\Data\Image\Region;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\WriteRequest;
use FredBradley\SmugMug\Support\Path;
use JsonSerializable;

/**
 * Mark a region on an image: POST /api/v2/image/{imageKey}!regions.
 *
 * Parameters (in the body):
 *  - X (required), Y (required), Width (required), Height (required): the region, as fractions of the image
 *  - Type (required): Face, AF Point, Crop or Cover Image
 */
class CreateImageRegionRequest extends WriteRequest
{
    protected ?string $dto = Region::class;

    /**
     * @param  array<mixed>|JsonSerializable  $body  SmugMug's field names and values, or a model.
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $imageKey, array|JsonSerializable $body = [], Options|array $options = [])
    {
        $this->setBody($body, $options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/image/'.Path::segment($this->imageKey).'!regions';
    }
}
