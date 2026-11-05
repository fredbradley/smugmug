<?php

namespace FredBradley\SmugMug\Data\Account;

use FredBradley\SmugMug\Data\Data;

/**
 * One file of a library asset, such as a RAW original.
 *
 * SmugMug sends it as: Component.
 */
readonly class Component extends Data
{
    /**
     * @param  array<string, mixed>  $Uris  The related endpoints, by name (see uri()).
     * @param  array<string, mixed>  $extra  The fields SmugMug sent that the model has no property for.
     */
    public function __construct(
        public ?string $ComponentType = null,
        public ?string $ComponentKey = null,
        public ?string $DownloadUrl = null,
        public ?string $Extension = null,
        public ?string $FileName = null,
        public ?int $FileSize = null,
        public ?string $MD5 = null,
        public ?string $Url = null,
        public ?string $Uri = null,
        public ?string $UriDescription = null,
        public array $Uris = [],
        public array $extra = [],
    ) {}
}
