<?php

namespace FredBradley\SmugMug\Exceptions;

use FredBradley\SmugMug\SmugMugApiException;

/**
 * The account's SmugMug subscription has lapsed (402).
 */
class PaymentRequiredException extends SmugMugApiException {}
