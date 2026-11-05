<?php

namespace FredBradley\SmugMug\Requests\User;

use FredBradley\SmugMug\Data\User\User;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ListRequest;

/**
 * Search SmugMug's users: GET /api/v2/user!search.
 *
 * Parameters (in the options):
 *  - q (required): the text to search for
 */
class SearchUsersRequest extends ListRequest
{
    protected ?string $dto = User::class;

    /**
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(Options|array $options = [])
    {
        $this->setOptions($options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/user!search';
    }
}
