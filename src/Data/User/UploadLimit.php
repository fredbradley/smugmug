<?php

namespace FredBradley\SmugMug\Data\User;

use FredBradley\SmugMug\Data\Data;

/**
 * What the user may upload of one kind of file: its types and largest size.
 *
 * SmugMug sends it as: UserUploadLimits.
 */
readonly class UploadLimit extends Data
{
    /**
     * @param  list<mixed>|null  $AcceptedFileTypes
     * @param  list<mixed>|null  $AdditionalKnownFileTypes
     * @param  array<string, mixed>  $Uris  The related endpoints, by name (see uri()).
     * @param  array<string, mixed>  $extra  The fields SmugMug sent that the model has no property for.
     */
    public function __construct(
        public ?string $Type = null,
        public ?string $MimeTypes = null,
        public ?array $AcceptedFileTypes = null,
        public ?array $AdditionalKnownFileTypes = null,
        public ?string $AdditionalKnownFileMessage = null,
        public ?string $AdditionalKnownFileLink = null,
        public ?int $MaxSize = null,
        public ?int $MaxPixels = null,
        public array $Uris = [],
        public array $extra = [],
    ) {}
}
