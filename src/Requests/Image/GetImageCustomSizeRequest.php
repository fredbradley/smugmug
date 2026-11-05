<?php

namespace FredBradley\SmugMug\Requests\Image;

use FredBradley\SmugMug\Data\Image\ImageSize;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ItemRequest;
use FredBradley\SmugMug\Support\Path;

/**
 * An image fitted to a size of your own: GET /api/v2/image/{imageKey}!sizecustom.
 *
 * Parameters (in the options):
 *  - width (required), height (required): the box to fit the image in, in pixels
 *  - fill: crop to fill the box
 */
class GetImageCustomSizeRequest extends ItemRequest
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
        return '/api/v2/image/'.Path::segment($this->imageKey).'!sizecustom';
    }
}
