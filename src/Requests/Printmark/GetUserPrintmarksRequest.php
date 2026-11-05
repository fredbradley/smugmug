<?php

namespace FredBradley\SmugMug\Requests\Printmark;

use FredBradley\SmugMug\Data\Social\Printmark;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ListRequest;
use FredBradley\SmugMug\Support\Path;

/**
 * A user's printmarks: GET /api/v2/user/{nickname}!printmarks.
 *
 * Parameters (in the options):
 *  - Unavailable: include the printmarks the plan no longer allows
 */
class GetUserPrintmarksRequest extends ListRequest
{
    protected ?string $dto = Printmark::class;

    /**
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $nickname, Options|array $options = [])
    {
        $this->setOptions($options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/user/'.Path::segment($this->nickname).'!printmarks';
    }
}
