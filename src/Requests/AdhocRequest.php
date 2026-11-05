<?php

namespace FredBradley\SmugMug\Requests;

use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Support\Path;
use Saloon\Enums\Method;
use Saloon\PaginationPlugin\Contracts\Paginatable;

/**
 * A GET or DELETE of any URI SmugMug serves, as SmugMug::get('/api/v2/album/SJT3DX!images') or
 * SmugMug::follow($album->uri('AlbumImages')) sends one. Its dto() is whatever the response's Locator says: a model
 * (or a Collection of them) when the package has one for it, arrays otherwise.
 */
class AdhocRequest extends SmugMugRequest implements Paginatable
{
    protected string $responseShape = self::AUTO;

    protected string $uri;

    /**
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(Method $method, string $uri, Options|array $options = [])
    {
        $this->method = $method;
        $this->uri = Path::uri($uri);
        $this->cacheable = $method === Method::GET;
        $this->setOptions($options);
    }

    public function resolveEndpoint(): string
    {
        return $this->uri;
    }
}
