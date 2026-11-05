<?php

namespace FredBradley\SmugMug\Data\Design;

use FredBradley\SmugMug\Data\Data;

/**
 * The settings of a profile widget: margins, icons and what it shows.
 *
 * SmugMug sends it as: PublishedProfileWidget, WorkingProfileWidget.
 */
readonly class ProfileWidget extends Data
{
    /**
     * @param  array<string, mixed>  $Uris  The related endpoints, by name (see uri()).
     * @param  array<string, mixed>  $extra  The fields SmugMug sent that the model has no property for.
     */
    public function __construct(
        public ?string $Name = null,
        public ?string $Type = null,
        public ?int $MarginTop = null,
        public ?int $MarginRight = null,
        public ?int $MarginBottom = null,
        public ?int $MarginLeft = null,
        public ?string $IconColor = null,
        public ?string $IconType = null,
        public ?bool $NewTab = null,
        public ?bool $ShowCoverPhoto = null,
        public ?bool $ShowEmail = null,
        public ?bool $ShowIcons = null,
        public ?bool $ShowProfilePhoto = null,
        public ?string $Uri = null,
        public ?string $UriDescription = null,
        public array $Uris = [],
        public array $extra = [],
    ) {}
}
