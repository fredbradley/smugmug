<?php

namespace FredBradley\SmugMug\Exceptions;

use FredBradley\SmugMug\SmugMugApiException;

/**
 * SmugMug failed, timed out or answered with something other than JSON (408, 5xx, or an HTML or empty body).
 */
class ServerErrorException extends SmugMugApiException {}
