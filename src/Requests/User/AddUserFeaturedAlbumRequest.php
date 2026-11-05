<?php

namespace FredBradley\SmugMug\Requests\User;

use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ActionRequest;
use FredBradley\SmugMug\Support\Path;
use JsonSerializable;

/**
 * Feature an album: POST /api/v2/user/{nickname}!featuredalbums.
 *
 * Parameters (in the body):
 *  - AlbumUri (required): the URI of the album to feature
 */
class AddUserFeaturedAlbumRequest extends ActionRequest
{
    /**
     * @param  array<mixed>|JsonSerializable  $body  SmugMug's field names and values, or a model.
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $nickname, array|JsonSerializable $body = [], Options|array $options = [])
    {
        $this->setBody($body, $options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/user/'.Path::segment($this->nickname).'!featuredalbums';
    }
}
