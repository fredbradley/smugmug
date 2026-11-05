<?php

namespace FredBradley\SmugMug\Requests\User;

use FredBradley\SmugMug\Data\Node\Node;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ItemRequest;
use FredBradley\SmugMug\Support\Path;

/**
 * The root node of a user's site: the top of their folders, albums and pages: GET /api/v2/user/{nickname}!rootnode.
 */
class GetUserRootNodeRequest extends ItemRequest
{
    protected ?string $dto = Node::class;

    /**
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $nickname, Options|array $options = [])
    {
        $this->setOptions($options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/user/'.Path::segment($this->nickname).'!rootnode';
    }
}
