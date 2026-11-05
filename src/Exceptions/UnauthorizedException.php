<?php

namespace FredBradley\SmugMug\Exceptions;

use FredBradley\SmugMug\SmugMugApiException;

/**
 * SmugMug did not accept the credentials, or the token lacks the Access or Permissions the operation needs (401).
 */
class UnauthorizedException extends SmugMugApiException {}
