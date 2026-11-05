<?php

namespace FredBradley\SmugMug\Enums;

/**
 * What an access token lets the application do, asked for on SmugMug's authorize page.
 */
enum Permissions: string
{
    /** Read only: every GET. SmugMug's default. */
    case Read = 'Read';

    /** Read, and add: create albums, folders and nodes, upload images, post comments. */
    case Add = 'Add';

    /** Read, add, change and delete: every PATCH and DELETE, moves, sorts and bulk image actions. */
    case Modify = 'Modify';
}
