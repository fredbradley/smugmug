<?php

namespace FredBradley\SmugMug\Exceptions;

use FredBradley\SmugMug\SmugMugApiException;

/**
 * The name or URL is already in use, for example when moving or renaming a node, album or folder (409).
 */
class ConflictException extends SmugMugApiException {}
