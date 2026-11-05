<?php

namespace FredBradley\SmugMug\Exceptions;

use FredBradley\SmugMug\SmugMugApiException;

/**
 * SmugMug refused the request as written: a missing or invalid parameter, a method the endpoint does not allow, or an Accept type it cannot answer with (400, 405, 406, 415, 422).
 */
class InvalidRequestException extends SmugMugApiException {}
