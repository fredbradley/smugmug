<?php

namespace FredBradley\SmugMug\Requests\User;

use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ActionRequest;
use FredBradley\SmugMug\Support\Path;
use JsonSerializable;

/**
 * Reorder a user's featured albums: POST /api/v2/user/{nickname}!sortfeaturedalbums.
 *
 * Parameters (in the body):
 *  - MoveUris (required): the featured albums to move (comma-separated URIs)
 *  - MoveLocation (required): Before or After
 *  - Uri (required): the featured album to move them before or after
 */
class SortUserFeaturedAlbumsRequest extends ActionRequest
{
    protected array $commaSeparated = ['MoveUris'];

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
        return '/api/v2/user/'.Path::segment($this->nickname).'!sortfeaturedalbums';
    }
}
