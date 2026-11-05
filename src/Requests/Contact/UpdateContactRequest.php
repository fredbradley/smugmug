<?php

namespace FredBradley\SmugMug\Requests\Contact;

use FredBradley\SmugMug\Data\User\Contact;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\WriteRequest;
use FredBradley\SmugMug\Support\Path;
use JsonSerializable;
use Saloon\Enums\Method;

/**
 * Change a contact: PATCH /api/v2/contact/{contactId}.
 *
 * Parameters (in the body):
 *  - Name, PrimaryEmail, AllowDuplicateEmail
 */
class UpdateContactRequest extends WriteRequest
{
    protected Method $method = Method::PATCH;

    protected ?string $dto = Contact::class;

    /**
     * @param  array<mixed>|JsonSerializable  $body  SmugMug's field names and values, or a model.
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $contactId, array|JsonSerializable $body = [], Options|array $options = [])
    {
        $this->setBody($body, $options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/contact/'.Path::segment($this->contactId);
    }
}
