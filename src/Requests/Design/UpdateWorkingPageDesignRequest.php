<?php

namespace FredBradley\SmugMug\Requests\Design;

use FredBradley\SmugMug\Data\Design\PageDesign;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\WriteRequest;
use FredBradley\SmugMug\Support\Path;
use JsonSerializable;
use Saloon\Enums\Method;

/**
 * Change the working version of a page design: PATCH /api/v2/pagedesign/{pageDesignId}/working.
 *
 * Parameters (in the body):
 *  - BackgroundType: none, photo, slideshow or video
 *  - BackgroundImageUri, BackgroundAlbumUri
 *  - BackgroundSlideshowSource: album, keyword, recent or selectedImages
 */
class UpdateWorkingPageDesignRequest extends WriteRequest
{
    protected Method $method = Method::PATCH;

    protected ?string $dto = PageDesign::class;

    /**
     * @param  array<mixed>|JsonSerializable  $body  SmugMug's field names and values, or a model.
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $pageDesignId, array|JsonSerializable $body = [], Options|array $options = [])
    {
        $this->setBody($body, $options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/pagedesign/'.Path::segment($this->pageDesignId).'/working';
    }
}
