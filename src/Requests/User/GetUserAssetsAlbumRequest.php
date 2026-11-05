<?php

namespace FredBradley\SmugMug\Requests\User;

use FredBradley\SmugMug\Data\Album\Album;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ItemRequest;
use FredBradley\SmugMug\Support\Path;

/**
 * The hidden album that holds one kind of the user's site assets: GET /api/v2/user/{nickname}!assetsalbum.
 *
 * Parameters (in the options):
 *  - Type (required): Logo Images, Profile Images, Cover Images, Printmark Images or Watermark Images
 */
class GetUserAssetsAlbumRequest extends ItemRequest
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
        return '/api/v2/user/'.Path::segment($this->nickname).'!assetsalbum';
    }
}
