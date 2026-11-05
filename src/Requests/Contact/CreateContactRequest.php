<?php

namespace FredBradley\SmugMug\Requests\Contact;

use FredBradley\SmugMug\Data\User\Contact;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\WriteRequest;
use FredBradley\SmugMug\Support\Path;
use JsonSerializable;

/**
 * Add a contact: POST /api/v2/user/{nickname}!contacts.
 *
 * Parameters (in the body):
 *  - PrimaryEmail (required)
 *  - Name
 *  - AllowDuplicateEmail: allow an address another contact has
 */
class CreateContactRequest extends WriteRequest
{
    protected ?string $dto = Contact::class;

    /**
     * @param  array<mixed>|JsonSerializable  $body  SmugMug's field names and values, or a model.
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $nickname, array|JsonSerializable $body = [], Options|array $options = [])
    {
        $this->setBody($body, $options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/user/'.Path::segment($this->nickname).'!contacts';
    }
}
