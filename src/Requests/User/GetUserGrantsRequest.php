<?php

namespace FredBradley\SmugMug\Requests\User;

use FredBradley\SmugMug\Data\User\Grant;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ListRequest;
use FredBradley\SmugMug\Support\Path;

/**
 * The access a user has granted to their contacts: GET /api/v2/user/{nickname}!grants.
 *
 * Parameters (in the options):
 *  - OwnerUri: only the grants on this album, folder or node
 */
class GetUserGrantsRequest extends ListRequest
{
    protected ?string $dto = Grant::class;

    /**
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $nickname, Options|array $options = [])
    {
        $this->setOptions($options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/user/'.Path::segment($this->nickname).'!grants';
    }
}
