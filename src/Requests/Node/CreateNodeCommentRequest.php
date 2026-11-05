<?php

namespace FredBradley\SmugMug\Requests\Node;

use FredBradley\SmugMug\Data\Social\Comment;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\WriteRequest;
use FredBradley\SmugMug\Support\Path;
use JsonSerializable;

/**
 * Comment on a node: POST /api/v2/node/{nodeId}!comments.
 *
 * Parameters (in the body):
 *  - Comment (required): the text
 *  - Name, Email, Link: who is commenting, for a comment without a token
 *  - Rating: a rating
 *  - SocialID, ServiceID: a social sign-in
 */
class CreateNodeCommentRequest extends WriteRequest
{
    protected ?string $dto = Comment::class;

    /**
     * @param  array<mixed>|JsonSerializable  $body  SmugMug's field names and values, or a model.
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $nodeId, array|JsonSerializable $body = [], Options|array $options = [])
    {
        $this->setBody($body, $options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/node/'.Path::segment($this->nodeId).'!comments';
    }
}
