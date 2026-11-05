<?php

namespace FredBradley\SmugMug\Requests\Image;

use FredBradley\SmugMug\Data\Image\Image;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\WriteRequest;
use FredBradley\SmugMug\Support\Path;
use JsonSerializable;
use Saloon\Enums\Method;

/**
 * Change an image's details: PATCH /api/v2/image/{imageKey}.
 *
 * Parameters (in the body):
 *  - Title, Caption
 *  - Keywords: semicolon-separated, or KeywordArray: a list
 *  - Latitude, Longitude, Altitude: where it was taken
 *  - Hidden: hide it from visitors
 */
class UpdateImageRequest extends WriteRequest
{
    protected Method $method = Method::PATCH;

    protected ?string $dto = Image::class;

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
        return '/api/v2/image/'.Path::segment($this->imageKey);
    }
}
