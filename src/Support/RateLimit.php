<?php

namespace FredBradley\SmugMug\Support;

use Carbon\CarbonImmutable;
use Saloon\Http\Response;

/**
 * SmugMug's rate-limit window, as the headers of a response describe it: X-RateLimit-Remaining (requests left),
 * X-RateLimit-Reset (when the window ends, in seconds since 1970) and, on a 429, Retry-After (seconds to wait).
 * SmugMug limits each user of each application, and the application alone for unauthenticated requests.
 */
final readonly class RateLimit
{
    public function __construct(
        public ?int $remaining = null,
        public ?CarbonImmutable $resetsAt = null,
        public ?int $retryAfter = null,
    ) {}

    public static function fromResponse(Response $response): self
    {
        $remaining = self::integer($response->header('X-RateLimit-Remaining'));
        $reset = self::integer($response->header('X-RateLimit-Reset'));
        $retryAfter = self::integer($response->header('Retry-After'));

        $resetsAt = $reset !== null && $reset > 0 ? CarbonImmutable::createFromTimestamp($reset) : null;

        if ($retryAfter === null && $response->status() === 429 && $resetsAt !== null) {
            $retryAfter = max(0, $reset - time());
        }

        return new self($remaining, $resetsAt, $retryAfter);
    }

    /**
     * Whether the window has no requests left.
     */
    public function exhausted(): bool
    {
        return $this->remaining !== null && $this->remaining <= 0;
    }

    private static function integer(mixed $value): ?int
    {
        if (is_array($value)) {
            $value = $value[0] ?? null;
        }

        return is_numeric($value) ? (int) $value : null;
    }
}
