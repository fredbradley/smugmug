<?php

namespace FredBradley\SmugMug\Resources;

use FredBradley\SmugMug\Data\Data;
use FredBradley\SmugMug\Data\User\Contact;
use FredBradley\SmugMug\Data\User\ContactEmail;
use FredBradley\SmugMug\Data\User\Grant;
use FredBradley\SmugMug\Listing;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\Contact\CreateContactRequest;
use FredBradley\SmugMug\Requests\Contact\DeleteContactRequest;
use FredBradley\SmugMug\Requests\Contact\GetContactEmailRequest;
use FredBradley\SmugMug\Requests\Contact\GetContactEmailsRequest;
use FredBradley\SmugMug\Requests\Contact\GetContactGrantsRequest;
use FredBradley\SmugMug\Requests\Contact\GetContactRequest;
use FredBradley\SmugMug\Requests\Contact\GetUserContactsRequest;
use FredBradley\SmugMug\Requests\Contact\UpdateContactRequest;

/**
 * Contacts, and the access granted to them, from SmugMug::contacts().
 */
class ContactResource extends BaseResource
{
    /**
     * A user's contacts: GET /api/v2/user/{nickname}!contacts.
     *
     *  - SortBy: Name or Email
     *  - SortOrder: ASC or DESC
     *  - Email: only this address
     *  - Search: the text to search for
     *
     * @param  Options|array<string, mixed>  $options
     * @return Listing<Contact>
     */
    public function forUser(string $nickname, Options|array $options = []): Listing
    {
        return $this->listing(fn (Options $o) => new GetUserContactsRequest($nickname, $o), $options);
    }

    /**
     * Add a contact: POST /api/v2/user/{nickname}!contacts.
     *
     *  - PrimaryEmail (required)
     *  - Name
     *  - AllowDuplicateEmail: allow an address another contact has
     *
     * @param  array<string, mixed>|Data  $fields
     * @param  Options|array<string, mixed>  $options
     */
    public function create(string $nickname, array|Data $fields, Options|array $options = []): ?Contact
    {
        return $this->fetch(new CreateContactRequest($nickname, $fields, $this->options($options)));
    }

    /**
     * A contact, by id: GET /api/v2/contact/{contactId}.
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function get(string $contactId, Options|array $options = []): ?Contact
    {
        return $this->fetch(new GetContactRequest($contactId, $this->options($options)));
    }

    /**
     * Change a contact: PATCH /api/v2/contact/{contactId}.
     *
     *  - Name, PrimaryEmail, AllowDuplicateEmail
     *
     * @param  array<string, mixed>|Data  $fields
     * @param  Options|array<string, mixed>  $options
     */
    public function update(string $contactId, array|Data $fields, Options|array $options = []): ?Contact
    {
        return $this->fetch(new UpdateContactRequest($contactId, $fields, $this->options($options)));
    }

    /**
     * Delete a contact: DELETE /api/v2/contact/{contactId}.
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function delete(string $contactId, Options|array $options = []): bool
    {
        return $this->perform(new DeleteContactRequest($contactId, $this->options($options)));
    }

    /**
     * A contact's email addresses: GET /api/v2/contact/{contactId}!emails.
     *
     * @param  Options|array<string, mixed>  $options
     * @return Listing<ContactEmail>
     */
    public function emails(string $contactId, Options|array $options = []): Listing
    {
        return $this->listing(fn (Options $o) => new GetContactEmailsRequest($contactId, $o), $options);
    }

    /**
     * One of a contact's email addresses: GET /api/v2/contact/{contactId}/email/{email}.
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function email(string $contactId, string $email, Options|array $options = []): ?ContactEmail
    {
        return $this->fetch(new GetContactEmailRequest($contactId, $email, $this->options($options)));
    }

    /**
     * The access granted to a contact: GET /api/v2/contact/{contactId}!grants.
     *
     * @param  Options|array<string, mixed>  $options
     * @return Listing<Grant>
     */
    public function grants(string $contactId, Options|array $options = []): Listing
    {
        return $this->listing(fn (Options $o) => new GetContactGrantsRequest($contactId, $o), $options);
    }
}
