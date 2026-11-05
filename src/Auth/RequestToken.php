<?php

namespace FredBradley\SmugMug\Auth;

use InvalidArgumentException;

/**
 * The temporary token the first step of OAuth gives: the user approves it on SmugMug, and the application trades it
 * (with the verifier SmugMug hands back) for their access token. It is good for one use within five minutes.
 *
 * Keep it between the redirect and the callback, in the session: toArray() and fromArray().
 */
final readonly class RequestToken
{
    public function __construct(
        public string $token,
        #[\SensitiveParameter] public string $secret,
        public bool $callbackConfirmed = false,
    ) {
        if ($token === '' || $secret === '') {
            throw new InvalidArgumentException('A request token needs its token and its secret.');
        }
    }

    /**
     * From SmugMug's form-encoded answer: oauth_token=…&oauth_token_secret=…&oauth_callback_confirmed=true.
     */
    public static function fromBody(string $body): self
    {
        parse_str(trim($body), $values);

        return new self(
            (string) ($values['oauth_token'] ?? ''),
            (string) ($values['oauth_token_secret'] ?? ''),
            ($values['oauth_callback_confirmed'] ?? '') === 'true',
        );
    }

    /**
     * @param  array{token: string, secret: string, callbackConfirmed?: bool}  $values
     */
    public static function fromArray(array $values): self
    {
        return new self($values['token'], $values['secret'], $values['callbackConfirmed'] ?? false);
    }

    /**
     * @return array{token: string, secret: string, callbackConfirmed: bool}
     */
    public function toArray(): array
    {
        return ['token' => $this->token, 'secret' => $this->secret, 'callbackConfirmed' => $this->callbackConfirmed];
    }

    /**
     * @return array<string, mixed>
     */
    public function __debugInfo(): array
    {
        return ['token' => $this->token, 'secret' => '[redacted]', 'callbackConfirmed' => $this->callbackConfirmed];
    }
}
