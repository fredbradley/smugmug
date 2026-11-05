<?php

namespace FredBradley\SmugMug\Requests\Image;

use FredBradley\SmugMug\Data\Image\ImageSize;
use FredBradley\SmugMug\Enums\Size;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ItemRequest;
use FredBradley\SmugMug\Support\Path;

/**
 * One size of an image: Tiny, Thumb, Small, Medium, Large, XLarge, X2Large to X5Large, 4K, 5K or Original: GET /api/v2/image/{imageKey}!size{size}.
 */
class GetImageSizeRequest extends ItemRequest
{
    protected ?string $dto = ImageSize::class;

    /**
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $imageKey, public readonly Size $size, Options|array $options = [])
    {
        $this->setOptions($options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/image/'.Path::segment($this->imageKey).'!size'.$this->size->value;
    }
}
