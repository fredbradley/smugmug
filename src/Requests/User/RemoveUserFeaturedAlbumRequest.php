<?php

namespace FredBradley\SmugMug\Requests\User;

use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ActionRequest;
use FredBradley\SmugMug\Support\Path;
use JsonSerializable;
use Saloon\Enums\Method;

/**
 * Stop featuring an album: DELETE /api/v2/user/{nickname}!featuredalbums.
 *
 * Parameters (in the body):
 *  - UserAlbumUri (required): the URI of the featured album to stop featuring
 */
class RemoveUserFeaturedAlbumRequest extends ActionRequest
{
    protected Method $method = Method::DELETE;

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
