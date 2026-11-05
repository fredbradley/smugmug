<?php

namespace FredBradley\SmugMug\Requests\Album;

use FredBradley\SmugMug\Data\Social\Comment;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\WriteRequest;
use FredBradley\SmugMug\Support\Path;
use JsonSerializable;

/**
 * Comment on an album: POST /api/v2/album/{albumKey}!comments.
 *
 * Parameters (in the body):
 *  - Comment (required): the text
 *  - Name, Email, Link: who is commenting, for a comment without a token
 *  - Rating: a rating
 *  - SocialID, ServiceID: a social sign-in
 */
class CreateAlbumCommentRequest extends WriteRequest
{
    protected ?string $dto = Comment::class;

    /**
     * @param  array<mixed>|JsonSerializable  $body  SmugMug's field names and values, or a model.
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $albumKey, array|JsonSerializable $body = [], Options|array $options = [])
    {
        $this->setBody($body, $options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/album/'.Path::segment($this->albumKey).'!comments';
    }
}
