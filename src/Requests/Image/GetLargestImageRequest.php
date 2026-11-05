<?php

namespace FredBradley\SmugMug\Requests\Image;

use FredBradley\SmugMug\Data\Image\ImageSize;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ItemRequest;
use FredBradley\SmugMug\Support\Path;

/**
 * The largest size of an image the credentials may see: GET /api/v2/image/{imageKey}!largestimage.
 *
 * Parameters (in the options):
 *  - MaxSize: the largest size to consider, such as ImageSizeX2Large
 */
class GetLargestImageRequest extends ItemRequest
{
    protected ?string $dto = ImageSize::class;

    /**
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $imageKey, Options|array $options = [])
    {
        $this->setOptions($options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/image/'.Path::segment($this->imageKey).'!largestimage';
    }
}
