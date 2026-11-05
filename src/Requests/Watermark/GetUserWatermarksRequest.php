<?php

namespace FredBradley\SmugMug\Requests\Watermark;

use FredBradley\SmugMug\Data\Social\Watermark;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ListRequest;
use FredBradley\SmugMug\Support\Path;

/**
 * A user's watermarks: GET /api/v2/user/{nickname}!watermarks.
 *
 * Parameters (in the options):
 *  - Unavailable: include the watermarks the plan no longer allows
 */
class GetUserWatermarksRequest extends ListRequest
{
    protected ?string $dto = Watermark::class;

    /**
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $nickname, Options|array $options = [])
    {
        $this->setOptions($options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/user/'.Path::segment($this->nickname).'!watermarks';
    }
}
