<?php

namespace FredBradley\SmugMug\Requests\Design;

use FredBradley\SmugMug\Data\Image\Image;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ItemRequest;
use FredBradley\SmugMug\Support\Path;

/**
 * A site design's highlight image: GET /api/v2/sitedesign/{siteDesignKey}!highlightimage.
 */
class GetSiteDesignHighlightImageRequest extends ItemRequest
{
    protected ?string $dto = Image::class;

    /**
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $siteDesignKey, Options|array $options = [])
    {
        $this->setOptions($options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/sitedesign/'.Path::segment($this->siteDesignKey).'!highlightimage';
    }
}
