<?php

namespace FredBradley\SmugMug\Requests\Design;

use FredBradley\SmugMug\Data\Design\UserSkin;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ItemRequest;
use FredBradley\SmugMug\Support\Path;

/**
 * The published version of a site skin: GET /api/v2/userskin/{userSkinId}/published.
 */
class GetPublishedUserSkinRequest extends ItemRequest
{
    protected ?string $dto = UserSkin::class;

    /**
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $userSkinId, Options|array $options = [])
    {
        $this->setOptions($options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/userskin/'.Path::segment($this->userSkinId).'/published';
    }
}
