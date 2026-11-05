<?php

namespace FredBradley\SmugMug;

use Closure;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ListRequest;
use FredBradley\SmugMug\Support\Payload;
use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;
use IteratorAggregate;
use JsonException;
use Traversable;

/**
 * A list SmugMug pages through, such as an album's images, which a resource returns unsent:
 *
 *     $album->images('SJT3DX')->get();      // one page: the start and count in the options, or SmugMug's default
 *     $album->images('SJT3DX')->lazy();     // every item, a page at a time, as they are iterated
 *     $album->images('SJT3DX')->all();      // every item, fetched at once
 *     $album->images('SJT3DX')->first();    // the first item, or null
 *     $album->images('SJT3DX')->total();    // how many there are
 *
 * Iterating a Listing (foreach) is lazy().
 *
 * @template TModel
 *
 * @implements IteratorAggregate<int, TModel>
 */
final class Listing implements IteratorAggregate
{
    /**
     * @param  Closure(Options): ListRequest  $request  Makes the request with the given options.
     */
    public function __construct(
        private readonly SmugMugConnector $connector,
        private readonly Closure $request,
        private readonly Options $options,
    ) {}

    /**
     * The request for one page, with the options given (plus any more).
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function request(Options|array $options = []): ListRequest
    {
        return ($this->request)($this->options->merge($options));
    }

    /**
     * One page of items: the start and count of the options, or SmugMug's default page.
     *
     * @return Collection<int, TModel>
     */
    public function get(): Collection
    {
        return $this->send($this->request());
    }

    /**
     * The given page (from 1) of $perPage items.
     *
     * @return Collection<int, TModel>
     */
    public function page(int $page, int $perPage = 100): Collection
    {
        return $this->send($this->request(Options::make()->page($page, $perPage)));
    }

    /**
     * The first item, or null when there is none.
     *
     * @return TModel|null
     */
    public function first(): mixed
    {
        return $this->send($this->request(Options::make()->count(1)))->first();
    }

    /**
     * How many items there are (Response.Pages.Total), from a one-item page.
     */
    public function total(): int
    {
        $response = $this->connector->send($this->request(Options::make()->count(1)));

        if (! $response->successful()) {
            throw SmugMugApiException::fromResponse($response);
        }

        try {
            $total = Payload::pages($response)['Total'] ?? null;

            return is_numeric($total) ? (int) $total : count(Payload::rawItems($response));
        } catch (JsonException) {
            throw new Exceptions\ServerErrorException($response);
        }
    }

    /**
     * Every item, fetched a page of $perPage at a time as they are iterated.
     *
     * @return LazyCollection<int, TModel>
     */
    public function lazy(?int $perPage = null): LazyCollection
    {
        /** @var LazyCollection<int, TModel> */
        return $this->paginator($perPage)->dtos();
    }

    /**
     * Every item, fetched at once.
     *
     * @return Collection<int, TModel>
     */
    public function all(?int $perPage = null): Collection
    {
        return $this->lazy($perPage)->collect();
    }

    /**
     * The paginator over every page, for its responses or a custom mapping.
     */
    public function paginator(?int $perPage = null): SmugMugPaginator
    {
        $options = $this->options->with('start', null)->with('count', null);

        return $this->connector->paginate(($this->request)($options), $perPage);
    }

    /**
     * @return Traversable<int, TModel>
     */
    public function getIterator(): Traversable
    {
        return $this->lazy()->getIterator();
    }

    /**
     * @return Collection<int, TModel>
     */
    private function send(ListRequest $request): Collection
    {
        $response = $this->connector->send($request);

        if (! $response->successful()) {
            throw SmugMugApiException::fromResponse($response);
        }

        try {
            return $response->dto();
        } catch (JsonException) {
            throw new Exceptions\ServerErrorException($response);
        }
    }
}
