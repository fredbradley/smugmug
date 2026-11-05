<?php

namespace FredBradley\SmugMug\Requests\Image;

use FredBradley\SmugMug\Data\Album\Album;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ListRequest;
use FredBradley\SmugMug\Support\Path;

/**
 * The albums an image is in: GET /api/v2/image/{imageKey}!albums.
 */
class GetImageAlbumsRequest extends ListRequest
{
    protected ?string $dto = Album::class;

    /**
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $imageKey, Options|array $options = [])
    {
        $this->setOptions($options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/image/'.Path::segment($this->imageKey).'!albums';
    }
}
