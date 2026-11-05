<?php

namespace FredBradley\SmugMug\Requests\Contact;

use FredBradley\SmugMug\Data\User\ContactEmail;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ItemRequest;
use FredBradley\SmugMug\Support\Path;

/**
 * One of a contact's email addresses: GET /api/v2/contact/{contactId}/email/{email}.
 */
class GetContactEmailRequest extends ItemRequest
{
    protected ?string $dto = ContactEmail::class;

    /**
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $contactId, public readonly string $email, Options|array $options = [])
    {
        $this->setOptions($options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/contact/'.Path::segment($this->contactId).'/email/'.Path::segment($this->email);
    }
}
