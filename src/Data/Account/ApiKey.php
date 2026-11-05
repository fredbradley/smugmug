<?php

namespace FredBradley\SmugMug\Data\Account;

use Carbon\CarbonImmutable;
use FredBradley\SmugMug\Data\Data;

/**
 * One of the user's API keys (applications).
 *
 * SmugMug sends it as: ApiKey.
 */
readonly class ApiKey extends Data
{
    protected const HIDDEN = ['APIKeySecret'];

    /**
     * @param  array<string, mixed>  $Uris  The related endpoints, by name (see uri()).
     * @param  array<string, mixed>  $extra  The fields SmugMug sent that the model has no property for.
     */
    public function __construct(
        public ?string $Name = null,
        public ?string $AppUrl = null,
        public ?string $CallbackUrl = null,
        public ?string $Type = null,
        public ?string $Platform = null,
        public ?string $UsedFor = null,
        public ?string $Description = null,
        public ?string $APIKeyID = null,
        public ?string $APIKeySecret = null,
        public ?int $AuthenticatedUserCount = null,
        public ?CarbonImmutable $Created = null,
        public ?string $Status = null,
        public ?string $Uri = null,
        public ?string $UriDescription = null,
        public array $Uris = [],
        public array $extra = [],
    ) {}
}
