<?php

namespace FredBradley\SmugMug\Resources;

use FredBradley\SmugMug\Data\Data;
use FredBradley\SmugMug\Data\Social\Comment;
use FredBradley\SmugMug\Listing;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\Comment\CreateCommentRequest;
use FredBradley\SmugMug\Requests\Comment\GetCommentRequest;
use FredBradley\SmugMug\Requests\Comment\LookupCommentsRequest;

/**
 * Comments, from SmugMug::comments().
 */
class CommentResource extends BaseResource
{
    /**
     * A comment, by id: GET /api/v2/comment/{commentId}.
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function get(string $commentId, Options|array $options = []): ?Comment
    {
        return $this->fetch(new GetCommentRequest($commentId, $this->options($options)));
    }

    /**
     * Post a comment: POST /api/v2/comment.
     *
     *  - Comment (required): the text
     *  - Name, Email, Link, Rating, SocialID, ServiceID
     *
     * @param  array<string, mixed>|Data  $fields
     * @param  Options|array<string, mixed>  $options
     */
    public function create(array|Data $fields, Options|array $options = []): ?Comment
    {
        return $this->fetch(new CreateCommentRequest($fields, $this->options($options)));
    }

    /**
     * The comments on an album or image by its numeric id, including those awaiting approval: GET /api/v2/comment!lookuptype.
     *
     *  - type (required): Album or Image
     *  - typeid (required): the album's or image's numeric id
     *  - status (required): All, Pending or Approved
     *
     * @param  Options|array<string, mixed>  $options
     * @return Listing<Comment>
     */
    public function lookup(Options|array $options = []): Listing
    {
        return $this->listing(fn (Options $o) => new LookupCommentsRequest($o), $options);
    }
}
