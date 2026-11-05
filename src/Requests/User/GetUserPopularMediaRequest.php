<?php

namespace FredBradley\SmugMug\Requests\User;

use FredBradley\SmugMug\Data\Image\Image;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ListRequest;
use FredBradley\SmugMug\Support\Path;

/**
 * A user's most viewed images: GET /api/v2/user/{nickname}!popularmedia.
 *
 * Parameters (in the options):
 *  - MediaType: All, Images or Videos
 */
class GetUserPopularMediaRequest extends ListRequest
{
    protected ?string $dto = Image::class;

    /**
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $nickname, Options|array $options = [])
    {
        $this->setOptions($options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/user/'.Path::segment($this->nickname).'!popularmedia';
    }
}
