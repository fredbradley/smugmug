<?php

namespace FredBradley\SmugMug\Requests\Contact;

use FredBradley\SmugMug\Data\User\Contact;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ListRequest;
use FredBradley\SmugMug\Support\Path;

/**
 * A user's contacts: GET /api/v2/user/{nickname}!contacts.
 *
 * Parameters (in the options):
 *  - SortBy: Name or Email
 *  - SortOrder: ASC or DESC
 *  - Email: only this address
 *  - Search: the text to search for
 */
class GetUserContactsRequest extends ListRequest
{
    protected ?string $dto = Contact::class;

    /**
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $nickname, Options|array $options = [])
    {
        $this->setOptions($options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/user/'.Path::segment($this->nickname).'!contacts';
    }
}
