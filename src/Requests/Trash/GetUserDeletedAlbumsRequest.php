<?php

namespace FredBradley\SmugMug\Requests\Trash;

use FredBradley\SmugMug\Data\Node\DeletedNode;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ListRequest;
use FredBradley\SmugMug\Support\Path;

/**
 * The albums in a user's trash: GET /api/v2/user/{nickname}!deletedalbums.
 */
class GetUserDeletedAlbumsRequest extends ListRequest
{
    protected ?string $dto = DeletedNode::class;

    /**
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $nickname, Options|array $options = [])
    {
        $this->setOptions($options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/user/'.Path::segment($this->nickname).'!deletedalbums';
    }
}
