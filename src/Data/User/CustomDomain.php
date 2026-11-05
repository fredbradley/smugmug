<?php

namespace FredBradley\SmugMug\Data\User;

use FredBradley\SmugMug\Data\Data;

/**
 * The custom domain a user's site is served on, and its certificate.
 *
 * SmugMug sends it as: CustomDomain.
 */
readonly class CustomDomain extends Data
{
    /**
     * @param  array<string, mixed>  $Uris  The related endpoints, by name (see uri()).
     * @param  array<string, mixed>  $extra  The fields SmugMug sent that the model has no property for.
     */
    public function __construct(
        public ?string $Domain = null,
        public ?string $Status = null,
        public ?string $CertificateStatus = null,
        public ?bool $IsEnabled = null,
        public ?string $StatusReason = null,
        public ?string $StatusMessage = null,
        public ?string $Uri = null,
        public ?string $UriDescription = null,
        public array $Uris = [],
        public array $extra = [],
    ) {}
}
