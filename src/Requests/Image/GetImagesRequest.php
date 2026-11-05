<?php

namespace FredBradley\SmugMug\Requests\Image;

use FredBradley\SmugMug\Data\Image\Image;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ListRequest;
use FredBradley\SmugMug\Support\Path;

/**
 * Several images at once (multi-get), by key: GET /api/v2/image/{imageKeys}.
 */
class GetImagesRequest extends ListRequest
{
    protected ?string $dto = Image::class;

    /**
     * @param  string|list<string>  $imageKeys
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string|array $imageKeys, Options|array $options = [])
    {
        $this->setOptions($options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/image/'.Path::segment($this->imageKeys);
    }
}
