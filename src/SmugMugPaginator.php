<?php

namespace FredBradley\SmugMug;

use FredBradley\SmugMug\Data\Data;
use FredBradley\SmugMug\Data\Models;
use FredBradley\SmugMug\Exceptions\ServerErrorException;
use FredBradley\SmugMug\Requests\SmugMugRequest;
use FredBradley\SmugMug\Support\Payload;
use Illuminate\Support\LazyCollection;
use JsonException;
use LogicException;
use Saloon\Http\Connector;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\PaginationPlugin\Paginator;

/**
 * Pages through a list with SmugMug's start (from 1) and count parameters, stopping when Response.Pages has no
 * NextPage, when a page is empty or short, or once the items fetched reach Pages.Total.
 */
class SmugMugPaginator extends Paginator
{
    protected ?int $perPageLimit = 100;

    protected ?string $lastPageFingerprint = null;

    protected ?Response $lastResponse = null;

    /** @var list<mixed> */
    protected array $lastItems = [];

    public function __construct(Connector $connector, Request $request, ?int $perPage = null)
    {
        parent::__construct($connector, $request);

        if ($perPage !== null) {
            if ($perPage < 1) {
                throw new LogicException('A page needs at least one item.');
            }

            $this->perPageLimit = $perPage;
        }
    }

    /**
     * How many items each page asks for.
     */
    public function perPage(): int
    {
        return $this->perPageLimit ?? 100;
    }

    /**
     * Every item on every page, as the request's model (or the model registered for each page's Locator).
     *
     * @return LazyCollection<int, Data>
     */
    public function dtos(): LazyCollection
    {
        $request = $this->getOriginalRequest();
        $dto = $request instanceof SmugMugRequest ? $request->dtoClass() : null;

        if ($dto !== null) {
            return $this->models($dto);
        }

        return LazyCollection::make(function () {
            foreach ($this as $response) {
                $model = Models::for(Payload::locator($response));

                if ($model === null) {
                    throw new LogicException('The package has no model for '.(Payload::locator($response) ?? 'this response').'; use collect() for its items as arrays.');
                }

                foreach ($this->getPageItems($response, $response->getRequest()) as $item) {
                    yield $model::from($item);
                }
            }
        });
    }

    /**
     * Every item on every page as the given model.
     *
     * @template TModel of Data
     *
     * @param  class-string<TModel>  $model
     * @return LazyCollection<int, TModel>
     */
    public function models(string $model): LazyCollection
    {
        return $this->collect()->map(fn (array $item) => $model::from($item));
    }

    protected function applyPagination(Request $request): Request
    {
        $perPage = $this->perPageLimit ?? 100;

        $request->query()->add('count', $perPage);
        $request->query()->add('start', ($this->page - 1) * $perPage + 1);

        return $request;
    }

    protected function isLastPage(Response $response): bool
    {
        $items = $this->getPageItems($response, $response->getRequest());

        if ($items === [] || ($this->perPageLimit !== null && count($items) < $this->perPageLimit)) {
            return true;
        }

        try {
            $pages = Payload::pages($response);
        } catch (JsonException) {
            return true;
        }

        if ($pages === null || ! isset($pages['NextPage'])) {
            return true;
        }

        return isset($pages['Total']) && is_numeric($pages['Total']) && $this->totalResults >= (int) $pages['Total'];
    }

    protected function onRewind(): void
    {
        $this->lastPageFingerprint = null;
        $this->lastResponse = null;
        $this->lastItems = [];
    }

    /**
     * @return list<mixed>
     */
    protected function getPageItems(Response $response, Request $request): array
    {
        try {
            if (! $response->successful() || ! Payload::succeeded($response)) {
                throw SmugMugApiException::fromResponse($response);
            }

            if ($response === $this->lastResponse) {
                return $this->lastItems;
            }

            $items = Payload::rawItems($response);

            // An endpoint that ignored start would answer the same page forever: a repeated page is the end.
            $fingerprint = sha1((string) json_encode($items));
            $this->lastResponse = $response;
            $this->lastItems = $fingerprint === $this->lastPageFingerprint ? [] : $items;
            $this->lastPageFingerprint = $fingerprint;

            return $this->lastItems;
        } catch (JsonException) {
            throw new ServerErrorException($response);
        }
    }

    /**
     * How many pages there are, from the Total SmugMug gives with each page.
     */
    protected function getTotalPages(Response $response): int
    {
        try {
            $pages = Payload::pages($response);
        } catch (JsonException) {
            throw new ServerErrorException($response);
        }

        $total = is_numeric($pages['Total'] ?? null) ? (int) $pages['Total'] : count($this->getPageItems($response, $response->getRequest()));

        return (int) max(1, ceil($total / ($this->perPageLimit ?? 100)));
    }
}
