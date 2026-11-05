<?php

namespace FredBradley\SmugMug\Requests\Image;

use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\DeleteRequest;
use FredBradley\SmugMug\Support\Path;

/**
 * Remove an image's watermark: DELETE /api/v2/image/{imageKey}!watermark.
 */
class RemoveImageWatermarkRequest extends DeleteRequest
{
    /**
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $imageKey, Options|array $options = [])
    {
        $this->setOptions($options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/image/'.Path::segment($this->imageKey).'!watermark';
    }
}
