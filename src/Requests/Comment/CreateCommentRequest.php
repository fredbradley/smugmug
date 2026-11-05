<?php

namespace FredBradley\SmugMug\Requests\Comment;

use FredBradley\SmugMug\Data\Social\Comment;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\WriteRequest;
use JsonSerializable;

/**
 * Post a comment: POST /api/v2/comment.
 *
 * Parameters (in the body):
 *  - Comment (required): the text
 *  - Name, Email, Link, Rating, SocialID, ServiceID
 */
class CreateCommentRequest extends WriteRequest
{
    protected ?string $dto = Comment::class;

    /**
     * @param  array<mixed>|JsonSerializable  $body  SmugMug's field names and values, or a model.
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(array|JsonSerializable $body = [], Options|array $options = [])
    {
        $this->setBody($body, $options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/comment';
    }
}
