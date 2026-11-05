<?php

namespace FredBradley\SmugMug\Requests\Contact;

use FredBradley\SmugMug\Data\User\Grant;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ListRequest;
use FredBradley\SmugMug\Support\Path;

/**
 * The access granted to a contact: GET /api/v2/contact/{contactId}!grants.
 */
class GetContactGrantsRequest extends ListRequest
{
    protected ?string $dto = Grant::class;

    /**
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $contactId, Options|array $options = [])
    {
        $this->setOptions($options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/contact/'.Path::segment($this->contactId).'!grants';
    }
}
