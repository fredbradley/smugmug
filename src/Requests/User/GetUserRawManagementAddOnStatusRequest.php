<?php

namespace FredBradley\SmugMug\Requests\User;

use FredBradley\SmugMug\Data\User\AddOnStatus;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ItemRequest;
use FredBradley\SmugMug\Support\Path;

/**
 * The state of a user's RAW file management add-on: GET /api/v2/user/{nickname}!rawmanagementaddonstatus.
 *
 * Parameters (in the options):
 *  - Currency: the currency to show prices in
 */
class GetUserRawManagementAddOnStatusRequest extends ItemRequest
{
    protected ?string $dto = AddOnStatus::class;

    /**
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $nickname, Options|array $options = [])
    {
        $this->setOptions($options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/user/'.Path::segment($this->nickname).'!rawmanagementaddonstatus';
    }
}
