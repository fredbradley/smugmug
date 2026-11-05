<?php

namespace FredBradley\SmugMug\Requests\User;

use FredBradley\SmugMug\Data\User\UploadLimit;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ListRequest;
use FredBradley\SmugMug\Support\Path;

/**
 * What the user may upload: accepted file types and largest sizes: GET /api/v2/user/{nickname}!uploadlimits.
 */
class GetUserUploadLimitsRequest extends ListRequest
{
    protected ?string $dto = UploadLimit::class;

    /**
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $nickname, Options|array $options = [])
    {
        $this->setOptions($options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/user/'.Path::segment($this->nickname).'!uploadlimits';
    }
}
