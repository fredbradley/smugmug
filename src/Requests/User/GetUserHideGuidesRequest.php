<?php

namespace FredBradley\SmugMug\Requests\User;

use FredBradley\SmugMug\Data\User\HideGuides;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ItemRequest;
use FredBradley\SmugMug\Support\Path;

/**
 * Whether SmugMug's guides are hidden for a user: GET /api/v2/user/{nickname}!hideguides.
 */
class GetUserHideGuidesRequest extends ItemRequest
{
    protected ?string $dto = HideGuides::class;

    /**
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $nickname, Options|array $options = [])
    {
        $this->setOptions($options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/user/'.Path::segment($this->nickname).'!hideguides';
    }
}
