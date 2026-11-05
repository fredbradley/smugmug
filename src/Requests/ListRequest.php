<?php

namespace FredBradley\SmugMug\Requests;

use Saloon\Enums\Method;
use Saloon\PaginationPlugin\Contracts\Paginatable;

/**
 * A GET for a page of objects: an album's images, a node's children. SmugMug answers one page (its own default
 * size unless the options give a count); SmugMugPaginator pages through them all.
 */
abstract class ListRequest extends SmugMugRequest implements Paginatable
{
    protected Method $method = Method::GET;

    protected string $responseShape = self::LIST;

    protected bool $cacheable = true;
}
