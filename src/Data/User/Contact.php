<?php

namespace FredBradley\SmugMug\Data\User;

use FredBradley\SmugMug\Data\Data;

/**
 * Someone in the user's contacts, to grant access to private galleries.
 *
 * SmugMug sends it as: Contact.
 */
readonly class Contact extends Data
{
    /**
     * @param  array<string, mixed>  $Uris  The related endpoints, by name (see uri()).
     * @param  array<string, mixed>  $extra  The fields SmugMug sent that the model has no property for.
     */
    public function __construct(
        public ?string $Name = null,
        public ?string $PrimaryEmail = null,
        public ?bool $AllowDuplicateEmail = null,
        public ?string $ContactID = null,
        public ?string $Uri = null,
        public ?string $UriDescription = null,
        public array $Uris = [],
        public array $extra = [],
    ) {}
}
