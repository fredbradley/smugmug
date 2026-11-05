<?php

namespace FredBradley\SmugMug\Requests\Comment;

use FredBradley\SmugMug\Data\Social\Comment;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ItemRequest;
use FredBradley\SmugMug\Support\Path;

/**
 * A comment, by id: GET /api/v2/comment/{commentId}.
 */
class GetCommentRequest extends ItemRequest
{
    protected ?string $dto = Comment::class;

    /**
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $commentId, Options|array $options = [])
    {
        $this->setOptions($options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/comment/'.Path::segment($this->commentId);
    }
}
