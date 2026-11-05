<?php

namespace FredBradley\SmugMug\Facades;

use FredBradley\SmugMug\SmugMugConnector;
use FredBradley\SmugMug\SmugMugService;
use Illuminate\Support\Facades\Facade;

/**
 * The default SmugMug connection, as configured in config/smugmug.php. Every method of SmugMugConnector is
 * available here; the resources are listed in HasResources.
 *
 * @mixin SmugMugConnector
 *
 * @see SmugMugService
 * @see SmugMugConnector
 */
class SmugMug extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'smugmug';
    }
}
