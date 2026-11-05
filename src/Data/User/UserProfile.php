<?php

namespace FredBradley\SmugMug\Data\User;

use FredBradley\SmugMug\Data\Data;

/**
 * A user's public profile: their bio and the links to their other sites.
 *
 * SmugMug sends it as: UserProfile.
 *
 * Deprecated by SmugMug, kept for older objects: ContactEmail, GooglePlus.
 */
readonly class UserProfile extends Data
{
    /**
     * @param  array<string, mixed>  $Uris  The related endpoints, by name (see uri()).
     * @param  array<string, mixed>  $extra  The fields SmugMug sent that the model has no property for.
     */
    public function __construct(
        public ?string $BioText = null,
        public ?string $FirstName = null,
        public ?string $LastName = null,
        public ?string $DisplayName = null,
        public ?string $ContactEmail = null,
        public ?bool $ValidEmail = null,
        public ?string $GooglePlus = null,
        public ?string $Facebook = null,
        public ?string $Twitter = null,
        public ?string $Instagram = null,
        public ?string $Blogger = null,
        public ?string $FiveHundredPX = null,
        public ?string $Flickr = null,
        public ?string $LinkedIn = null,
        public ?string $Pinterest = null,
        public ?string $Tumblr = null,
        public ?string $Vimeo = null,
        public ?string $Wordpress = null,
        public ?string $YouTube = null,
        public ?string $Custom = null,
        public ?string $Uri = null,
        public ?string $UriDescription = null,
        public ?string $ResponseLevel = null,
        public array $Uris = [],
        public array $extra = [],
    ) {}
}
