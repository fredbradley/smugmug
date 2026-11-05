<?php

namespace FredBradley\SmugMug\Resources;

use Closure;
use FredBradley\SmugMug\Exceptions\NotFoundException;
use FredBradley\SmugMug\Exceptions\ServerErrorException;
use FredBradley\SmugMug\Listing;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ListRequest;
use FredBradley\SmugMug\Requests\SmugMugRequest;
use FredBradley\SmugMug\SmugMugApiException;
use FredBradley\SmugMug\SmugMugConnector;
use JsonException;

/**
 * What every resource shares: its connector, options for every request it sends (expand(), filter()), and sending
 * a request for its model.
 */
abstract class BaseResource
{
    protected Options $defaults;

    public function __construct(protected SmugMugConnector $connector)
    {
        $this->defaults = new Options;
    }

    public function connector(): SmugMugConnector
    {
        return $this->connector;
    }

    /**
     * Expand these related objects in every response (_expand), as in ->expand('HighlightImage')->get($key).
     */
    public function expand(string ...$uris): static
    {
        return $this->withOptions(Options::make()->expand(...$uris));
    }

    /**
     * Return only these fields of every object (_filter).
     */
    public function filter(string ...$fields): static
    {
        return $this->withOptions(Options::make()->filter(...$fields));
    }

    /**
     * Return only these Uris of every object (_filteruri); with none, no Uris at all.
     */
    public function filterUri(string ...$uris): static
    {
        return $this->withOptions(Options::make()->filterUri(...$uris));
    }

    /**
     * Send these options with every request.
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function withOptions(Options|array $options): static
    {
        $resource = clone $this;
        $resource->defaults = $this->defaults->merge($options);

        return $resource;
    }

    /**
     * The options a request is sent with: the resource's, with the call's on top.
     *
     * @param  Options|array<string, mixed>  $options
     */
    protected function options(Options|array $options): Options
    {
        return $this->defaults->merge($options);
    }

    /**
     * Send a request and return what SmugMug answered as the request's model, throwing for an error status or a
     * failed envelope.
     */
    protected function fetch(SmugMugRequest $request): mixed
    {
        $response = $this->connector->send($request);

        if (! $response->successful()) {
            throw SmugMugApiException::fromResponse($response);
        }

        try {
            return $response->dto();
        } catch (JsonException) {
            throw new ServerErrorException($response);
        }
    }

    /**
     * As fetch(), but null when SmugMug has no such object (or the credentials may not see it).
     */
    protected function fetchOrNull(SmugMugRequest $request): mixed
    {
        try {
            return $this->fetch($request);
        } catch (NotFoundException) {
            return null;
        }
    }

    /**
     * As fetch(), for an operation whose answer is the envelope alone: true once SmugMug said it succeeded.
     */
    protected function perform(SmugMugRequest $request): bool
    {
        $this->fetch($request);

        return true;
    }

    /**
     * A list, unsent: get() one page, lazy() or all() every item.
     *
     * @param  Closure(Options): ListRequest  $request
     * @param  Options|array<string, mixed>  $options
     * @return Listing<mixed>
     */
    protected function listing(Closure $request, Options|array $options): Listing
    {
        return new Listing($this->connector, $request, $this->options($options));
    }
}
