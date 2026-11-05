<?php

namespace FredBradley\SmugMug\Resources;

use FredBradley\SmugMug\Data\Social\Guide;
use FredBradley\SmugMug\Data\User\GuideState;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\Guide\GetGuideRequest;
use FredBradley\SmugMug\Requests\Guide\GetGuideUserStateRequest;

/**
 * SmugMug's in-app guides, from SmugMug::guides().
 */
class GuideResource extends BaseResource
{
    /**
     * One of SmugMug's guides, by id: GET /api/v2/guide/{guideId}.
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function get(string $guideId, Options|array $options = []): ?Guide
    {
        return $this->fetch(new GetGuideRequest($guideId, $this->options($options)));
    }

    /**
     * Whether a user has completed a guide: GET /api/v2/guide/{guideId}!userstate.
     *
     *  - NickName: the user
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function userState(string $guideId, Options|array $options = []): ?GuideState
    {
        return $this->fetch(new GetGuideUserStateRequest($guideId, $this->options($options)));
    }
}
