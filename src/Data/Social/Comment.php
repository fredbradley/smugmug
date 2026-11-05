<?php

namespace FredBradley\SmugMug\Data\Social;

use FredBradley\SmugMug\Data\Data;

/**
 * A comment on an album, image or node.
 *
 * SmugMug sends it as: Comment.
 */
readonly class Comment extends Data
{
    /**
     * @param  array<string, mixed>  $Uris  The related endpoints, by name (see uri()).
     * @param  array<string, mixed>  $extra  The fields SmugMug sent that the model has no property for.
     */
    public function __construct(
        public ?string $Comment = null,
        public ?string $Name = null,
        public ?string $Email = null,
        public ?string $Link = null,
        public ?int $Rating = null,
        public ?string $Uri = null,
        public ?string $UriDescription = null,
        public array $Uris = [],
        public array $extra = [],
    ) {}
}
