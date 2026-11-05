<?php

namespace FredBradley\SmugMug\Requests\User;

use FredBradley\SmugMug\Data\User\GuideState;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ListRequest;
use FredBradley\SmugMug\Support\Path;

/**
 * Which of SmugMug's guides a user has completed: GET /api/v2/user/{nickname}!guides.
 *
 * Parameters (in the options):
 *  - OnlyAutorun: only the guides that start by themselves
 *  - IncludeInvisible: include hidden guides
 */
class GetUserGuideStatesRequest extends ListRequest
{
    protected ?string $dto = GuideState::class;

    /**
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $nickname, Options|array $options = [])
    {
        $this->setOptions($options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/user/'.Path::segment($this->nickname).'!guides';
    }
}
