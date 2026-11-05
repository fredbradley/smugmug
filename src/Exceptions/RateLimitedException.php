<?php

namespace FredBradley\SmugMug\Exceptions;

use FredBradley\SmugMug\SmugMugApiException;
use FredBradley\SmugMug\Support\RateLimit;

/**
 * Too many requests in SmugMug's current rate-limit window (429). Wait retryAfter() seconds before trying again.
 */
class RateLimitedException extends SmugMugApiException
{
    /**
     * Seconds until the window ends, from SmugMug's Retry-After header (or its X-RateLimit-Reset), or null when it
     * gave neither.
     */
    public function retryAfter(): ?int
    {
        return RateLimit::fromResponse($this->getResponse())->retryAfter;
    }
}
