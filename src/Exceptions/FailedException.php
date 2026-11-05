<?php

namespace FredBradley\SmugMug\Exceptions;

use FredBradley\SmugMug\SmugMugApiException;

/**
 * SmugMug answered with a success status but its envelope says the request failed, as the uploader does with {"stat": "fail"}.
 */
class FailedException extends SmugMugApiException {}
