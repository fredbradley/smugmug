<?php

namespace FredBradley\SmugMug\Requests\User;

use FredBradley\SmugMug\Data\Album\Album;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ListRequest;
use FredBradley\SmugMug\Support\Path;

/**
 * Every album a user has (that the credentials may see): GET /api/v2/user/{nickname}!albums.
 *
 * Parameters (in the options):
 *  - Order: Last Updated (Descending), Last Updated (Ascending), Date Added (Descending) or Date Added (Ascending)
 */
class GetUserAlbumsRequest extends ListRequest
{
    protected ?string $dto = Album::class;

    /**
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $nickname, Options|array $options = [])
    {
        $this->setOptions($options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/user/'.Path::segment($this->nickname).'!albums';
    }
}
