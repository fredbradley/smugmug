<?php

namespace FredBradley\SmugMug\Requests\User;

use FredBradley\SmugMug\Data\User\Task;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ListRequest;
use FredBradley\SmugMug\Support\Path;

/**
 * The background tasks SmugMug is running for a user: GET /api/v2/user/{nickname}!tasks.
 *
 * Parameters (in the options):
 *  - status: Any, Pending, Done or Error
 */
class GetUserTasksRequest extends ListRequest
{
    protected ?string $dto = Task::class;

    /**
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $nickname, Options|array $options = [])
    {
        $this->setOptions($options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/user/'.Path::segment($this->nickname).'!tasks';
    }
}
