<?php

namespace FredBradley\SmugMug\Data\Account;

use FredBradley\SmugMug\Data\Data;

/**
 * A label on a library asset.
 *
 * SmugMug sends it as: LabelKey.
 */
readonly class Label extends Data
{
    /**
     * @param  array<string, mixed>  $Uris  The related endpoints, by name (see uri()).
     * @param  array<string, mixed>  $extra  The fields SmugMug sent that the model has no property for.
     */
    public function __construct(
        public ?string $Uri = null,
        public ?string $UriDescription = null,
        public array $Uris = [],
        public array $extra = [],
    ) {}
}
