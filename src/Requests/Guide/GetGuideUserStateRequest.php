<?php

namespace FredBradley\SmugMug\Requests\Guide;

use FredBradley\SmugMug\Data\User\GuideState;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ItemRequest;
use FredBradley\SmugMug\Support\Path;

/**
 * Whether a user has completed a guide: GET /api/v2/guide/{guideId}!userstate.
 *
 * Parameters (in the options):
 *  - NickName: the user
 */
class GetGuideUserStateRequest extends ItemRequest
{
    protected ?string $dto = GuideState::class;

    /**
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $guideId, Options|array $options = [])
    {
        $this->setOptions($options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/guide/'.Path::segment($this->guideId).'!userstate';
    }
}
