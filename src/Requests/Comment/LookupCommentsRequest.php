<?php

namespace FredBradley\SmugMug\Requests\Comment;

use FredBradley\SmugMug\Data\Social\Comment;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ListRequest;

/**
 * The comments on an album or image by its numeric id, including those awaiting approval: GET /api/v2/comment!lookuptype.
 *
 * Parameters (in the options):
 *  - type (required): Album or Image
 *  - typeid (required): the album's or image's numeric id
 *  - status (required): All, Pending or Approved
 */
class LookupCommentsRequest extends ListRequest
{
    protected ?string $dto = Comment::class;

    /**
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(Options|array $options = [])
    {
        $this->setOptions($options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/comment!lookuptype';
    }
}
