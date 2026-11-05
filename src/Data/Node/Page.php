<?php

namespace FredBradley\SmugMug\Data\Node;

use FredBradley\SmugMug\Data\Data;

/**
 * A page of a user's site.
 *
 * SmugMug sends it as: Page.
 */
readonly class Page extends Data
{
    protected const HIDDEN = ['Password'];

    /**
     * @param  array<string, mixed>  $Uris  The related endpoints, by name (see uri()).
     * @param  array<string, mixed>  $extra  The fields SmugMug sent that the model has no property for.
     */
    public function __construct(
        public ?string $Name = null,
        public ?string $UrlName = null,
        public ?string $Privacy = null,
        public ?string $SecurityType = null,
        public ?string $Password = null,
        public ?string $PasswordHint = null,
        public ?string $UrlPath = null,
        public ?string $Uri = null,
        public ?string $WebUri = null,
        public ?string $UriDescription = null,
        public array $Uris = [],
        public array $extra = [],
    ) {}
}
