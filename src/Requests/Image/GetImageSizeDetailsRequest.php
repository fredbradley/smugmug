<?php

namespace FredBradley\SmugMug\Requests\Image;

use FredBradley\SmugMug\Data\Image\ImageSizeDetails;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ItemRequest;
use FredBradley\SmugMug\Support\Path;

/**
 * Every size of an image in detail, with which may be used: GET /api/v2/image/{imageKey}!sizedetails.
 *
 * Parameters (in the options):
 *  - PrefetchSizes: the sizes to have SmugMug prepare now
 */
class GetImageSizeDetailsRequest extends ItemRequest
{
    protected ?string $dto = ImageSizeDetails::class;

    /**
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $imageKey, Options|array $options = [])
    {
        $this->setOptions($options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/image/'.Path::segment($this->imageKey).'!sizedetails';
    }
}
