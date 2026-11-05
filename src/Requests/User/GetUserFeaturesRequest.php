<?php

namespace FredBradley\SmugMug\Requests\User;

use FredBradley\SmugMug\Data\User\Features;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ItemRequest;
use FredBradley\SmugMug\Support\Path;

/**
 * What a user's plan lets them do: GET /api/v2/user/{nickname}!features.
 */
class GetUserFeaturesRequest extends ItemRequest
{
    protected ?string $dto = Features::class;

    /**
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $nickname, Options|array $options = [])
    {
        $this->setOptions($options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/user/'.Path::segment($this->nickname).'!features';
    }
}
