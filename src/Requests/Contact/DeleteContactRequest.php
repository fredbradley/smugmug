<?php

namespace FredBradley\SmugMug\Requests\Contact;

use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\DeleteRequest;
use FredBradley\SmugMug\Support\Path;

/**
 * Delete a contact: DELETE /api/v2/contact/{contactId}.
 */
class DeleteContactRequest extends DeleteRequest
{
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
