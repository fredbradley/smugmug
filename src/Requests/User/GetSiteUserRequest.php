<?php

namespace FredBradley\SmugMug\Requests\User;

use FredBradley\SmugMug\Data\User\User;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ItemRequest;

/**
 * The user whose site the request is made on (on a custom domain): GET /api/v2!siteuser.
 */
class GetSiteUserRequest extends ItemRequest
{
    protected ?string $dto = User::class;

    /**
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(Options|array $options = [])
    {
        $this->setOptions($options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2!siteuser';
    }
}
