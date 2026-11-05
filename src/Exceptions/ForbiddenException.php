<?php

namespace FredBradley\SmugMug\Exceptions;

use FredBradley\SmugMug\SmugMugApiException;

/**
 * SmugMug understood the request but will not allow it, for example on another user's private content (403).
 */
class ForbiddenException extends SmugMugApiException {}
