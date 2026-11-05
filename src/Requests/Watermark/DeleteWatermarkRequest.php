<?php

namespace FredBradley\SmugMug\Requests\Watermark;

use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\DeleteRequest;
use FredBradley\SmugMug\Support\Path;

/**
 * Delete a watermark: DELETE /api/v2/watermark/{watermarkId}.
 */
class DeleteWatermarkRequest extends DeleteRequest
{
    /**
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $watermarkId, Options|array $options = [])
    {
        $this->setOptions($options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/watermark/'.Path::segment($this->watermarkId);
    }
}
