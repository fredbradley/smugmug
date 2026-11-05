<?php

namespace FredBradley\SmugMug\Data\User;

use FredBradley\SmugMug\Data\Data;

/**
 * The keywords a user (or one of their nodes) uses most.
 *
 * SmugMug sends it as: UserTopKeywords.
 */
readonly class TopKeywords extends Data
{
    /**
     * @param  list<mixed>|null  $TopKeywords
     * @param  array<string, mixed>  $Uris  The related endpoints, by name (see uri()).
     * @param  array<string, mixed>  $extra  The fields SmugMug sent that the model has no property for.
     */
    public function __construct(
        public ?array $TopKeywords = null,
        public ?string $Uri = null,
        public ?string $UriDescription = null,
        public array $Uris = [],
        public array $extra = [],
    ) {}
}
