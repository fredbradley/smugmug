<?php

namespace FredBradley\SmugMug\Requests\Contact;

use FredBradley\SmugMug\Data\User\ContactEmail;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ListRequest;
use FredBradley\SmugMug\Support\Path;

/**
 * A contact's email addresses: GET /api/v2/contact/{contactId}!emails.
 */
class GetContactEmailsRequest extends ListRequest
{
    protected ?string $dto = ContactEmail::class;

    /**
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $contactId, Options|array $options = [])
    {
        $this->setOptions($options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/contact/'.Path::segment($this->contactId).'!emails';
    }
}
