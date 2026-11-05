<?php

namespace FredBradley\SmugMug\Data\Social;

use FredBradley\SmugMug\Data\Data;

/**
 * A printmark: a mark (such as a logo) printed on the user's prints.
 *
 * SmugMug sends it as: Printmark.
 */
readonly class Printmark extends Data
{
    /**
     * @param  array<string, mixed>  $Uris  The related endpoints, by name (see uri()).
     * @param  array<string, mixed>  $extra  The fields SmugMug sent that the model has no property for.
     */
    public function __construct(
        public ?string $Name = null,
        public ?string $ImageKey = null,
        public ?int $Dissolve = null,
        public ?int $Gap = null,
        public ?string $Location = null,
        public ?int $Scale = null,
        public ?string $Uri = null,
        public ?string $UriDescription = null,
        public array $Uris = [],
        public array $extra = [],
    ) {}
}
