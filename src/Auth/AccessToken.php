<?php

namespace FredBradley\SmugMug\Auth;

use InvalidArgumentException;

/**
 * A user's access token and secret: what SmugMug::withToken() signs their requests with. SmugMug's tokens don't
 * expire; the user can revoke one from their account settings, after which requests answer 401.
 *
 * Store both halves as you would a password (encrypted, as Laravel's encrypted cast does), never in a log.
 */
final readonly class AccessToken
{
    public function __construct(
        #[\SensitiveParameter] public string $token,
        #[\SensitiveParameter] public string $secret,
    ) {
        if ($token === '' || $secret === '') {
            throw new InvalidArgumentException('An access token needs its token and its secret.');
        }
    }

    /**
     * From SmugMug's form-encoded answer: oauth_token=…&oauth_token_secret=….
     */
    public static function fromBody(string $body): self
    {
        parse_str(trim($body), $values);

        return new self((string) ($values['oauth_token'] ?? ''), (string) ($values['oauth_token_secret'] ?? ''));
    }

    /**
     * @return array{token: string, secret: string}
     */
    public function toArray(): array
    {
        return ['token' => $this->token, 'secret' => $this->secret];
    }

    /**
     * @return array<string, mixed>
     */
    public function __debugInfo(): array
    {
        return ['token' => '[redacted]', 'secret' => '[redacted]'];
    }
}
