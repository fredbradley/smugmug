<?php

namespace FredBradley\SmugMug\Enums;

/**
 * What an access token lets the application see, asked for on SmugMug's authorize page.
 */
enum Access: string
{
    /** Only what anyone may see: public albums, images and profiles. SmugMug's default. */
    case Public = 'Public';

    /** Everything the user may see, including private and unlisted albums, and their account details. */
    case Full = 'Full';
}
