<?php

namespace FredBradley\SmugMug\Requests\User;

use FredBradley\SmugMug\Data\User\Flags;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ItemRequest;
use FredBradley\SmugMug\Support\Path;

/**
 * The feature flags set for a user: GET /api/v2/user/{nickname}!flags.
 *
 * Parameters (in the options):
 *  - prefix: only the flags whose key starts with this
 */
class GetUserFlagsRequest extends ItemRequest
{
    protected ?string $dto = Flags::class;

    /**
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $nickname, Options|array $options = [])
    {
        $this->setOptions($options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/user/'.Path::segment($this->nickname).'!flags';
    }
}
