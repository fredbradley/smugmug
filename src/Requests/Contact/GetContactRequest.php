<?php

namespace FredBradley\SmugMug\Requests\Contact;

use FredBradley\SmugMug\Data\User\Contact;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ItemRequest;
use FredBradley\SmugMug\Support\Path;

/**
 * A contact, by id: GET /api/v2/contact/{contactId}.
 */
class GetContactRequest extends ItemRequest
{
    protected ?string $dto = Contact::class;

    /**
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $contactId, Options|array $options = [])
    {
        $this->setOptions($options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/contact/'.Path::segment($this->contactId);
    }
}
