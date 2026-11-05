<?php

namespace FredBradley\SmugMug\Requests\Image;

use FredBradley\SmugMug\Data\Image\ColorPreview;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ItemRequest;
use FredBradley\SmugMug\Support\Path;

/**
 * An image's size with a colour effect applied: GET /api/v2/image/{imageKey}!color.
 *
 * Parameters (in the options):
 *  - Color: Auto, Tan, Sepia, B/W or Negative
 */
class GetImageColorPreviewRequest extends ItemRequest
{
    protected ?string $dto = ColorPreview::class;

    /**
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $imageKey, Options|array $options = [])
    {
        $this->setOptions($options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/image/'.Path::segment($this->imageKey).'!color';
    }
}
