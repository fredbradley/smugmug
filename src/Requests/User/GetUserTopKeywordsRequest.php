<?php

namespace FredBradley\SmugMug\Requests\User;

use FredBradley\SmugMug\Data\User\TopKeywords;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ItemRequest;
use FredBradley\SmugMug\Support\Path;

/**
 * The keywords a user uses most: GET /api/v2/user/{nickname}!topkeywords.
 *
 * Parameters (in the options):
 *  - NumKeywords: how many keywords
 *  - NodeURI: only the keywords under this node
 */
class GetUserTopKeywordsRequest extends ItemRequest
{
    protected ?string $dto = TopKeywords::class;

    /**
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $nickname, Options|array $options = [])
    {
        $this->setOptions($options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/user/'.Path::segment($this->nickname).'!topkeywords';
    }
}
