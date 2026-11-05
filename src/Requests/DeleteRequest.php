<?php

namespace FredBradley\SmugMug\Requests;

use Saloon\Enums\Method;

/**
 * A DELETE of one object. SmugMug answers with the envelope alone.
 */
abstract class DeleteRequest extends SmugMugRequest
{
    protected Method $method = Method::DELETE;

    protected string $responseShape = self::NONE;
}
