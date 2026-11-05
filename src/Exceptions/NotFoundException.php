<?php

namespace FredBradley\SmugMug\Exceptions;

use FredBradley\SmugMug\SmugMugApiException;

/**
 * SmugMug has no such object, or the credentials may not know it exists (404).
 */
class NotFoundException extends SmugMugApiException {}
