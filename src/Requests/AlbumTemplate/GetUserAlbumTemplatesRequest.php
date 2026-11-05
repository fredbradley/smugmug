<?php

namespace FredBradley\SmugMug\Requests\AlbumTemplate;

use FredBradley\SmugMug\Data\Album\AlbumTemplate;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ListRequest;
use FredBradley\SmugMug\Support\Path;

/**
 * A user's album templates: GET /api/v2/user/{nickname}!albumtemplates.
 *
 * Parameters (in the options):
 *  - SortBy: Date Created
 *  - SortOrder: Ascending or Descending
 */
class GetUserAlbumTemplatesRequest extends ListRequest
{
    protected ?string $dto = AlbumTemplate::class;

    /**
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $nickname, Options|array $options = [])
    {
        $this->setOptions($options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/user/'.Path::segment($this->nickname).'!albumtemplates';
    }
}
