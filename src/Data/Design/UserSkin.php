<?php

namespace FredBradley\SmugMug\Data\Design;

use FredBradley\SmugMug\Data\Data;

/**
 * A site skin: its theme colours and custom CSS.
 *
 * SmugMug sends it as: PublishedUserSkin, WorkingUserSkin.
 */
readonly class UserSkin extends Data
{
    /**
     * @param  array<string, mixed>  $Uris  The related endpoints, by name (see uri()).
     * @param  array<string, mixed>  $extra  The fields SmugMug sent that the model has no property for.
     */
    public function __construct(
        public ?string $Name = null,
        public ?string $ThemeColorAccent = null,
        public ?string $ThemeColorBackground = null,
        public ?string $ThemeColorPrimary = null,
        public ?string $ThemeColorText = null,
        public ?string $SkinColorTitle = null,
        public ?string $SkinColorFormFieldBackground = null,
        public ?string $SkinColorFormFieldBackgroundFocus = null,
        public ?string $SkinColorFormFieldBorder = null,
        public ?string $SkinColorFormFieldBorderFocus = null,
        public ?string $SkinColorFormFieldLabel = null,
        public ?string $SkinColorFormFieldText = null,
        public ?bool $IsTrustedCSS = null,
        public ?string $CustomCSS = null,
        public ?string $Uri = null,
        public ?string $UriDescription = null,
        public array $Uris = [],
        public array $extra = [],
    ) {}
}
