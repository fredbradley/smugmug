<?php

namespace FredBradley\SmugMug\Requests\Image;

use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ActionRequest;
use JsonSerializable;
use Saloon\Enums\Method;

/**
 * Remove the watermark from several images: DELETE /api/v2/image!watermark.
 *
 * Parameters (in the body):
 *  - WatermarkUris (required): the images (a list of URIs)
 */
class RemoveImagesWatermarkRequest extends ActionRequest
{
    protected Method $method = Method::DELETE;

    protected array $commaSeparated = ['WatermarkUris'];

    /**
     * @param  array<mixed>|JsonSerializable  $body  SmugMug's field names and values, or a model.
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(array|JsonSerializable $body = [], Options|array $options = [])
    {
        $this->setBody($body, $options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/image!watermark';
    }
}
