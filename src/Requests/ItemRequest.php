<?php

namespace FredBradley\SmugMug\Requests;

use Saloon\Enums\Method;

/**
 * A GET for one object: a user, an album, an image's sizes.
 */
abstract class ItemRequest extends SmugMugRequest
{
    protected Method $method = Method::GET;

    protected string $responseShape = self::ONE;

    protected bool $cacheable = true;
}
