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
 * NextPage, or once the pages asked for reach Pages.Total. SmugMug can answer a page with fewer items than it was
 * asked for (or none) and still have more after it, as in big albums, so a short page is only taken as the end when
 * the response has no Pages to say otherwise.
 */
class SmugMugPaginator extends Paginator
{
    protected ?int $perPageLimit = 100;

    protected ?string $lastPageFingerprint = null;

    protected bool $repeatedPage = false;

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

        if ($this->repeatedPage) {
            return true;
        }

        try {
            $pages = Payload::pages($response);
        } catch (JsonException) {
            return true;
        }

        if ($pages === null) {
            return $items === [] || ($this->perPageLimit !== null && count($items) < $this->perPageLimit);
        }

        if (! isset($pages['NextPage'])) {
            return true;
        }

        if (! isset($pages['Total']) || ! is_numeric($pages['Total'])) {
            return false;
        }

        // The last place this page asked for, counting the items SmugMug left out of it.
        $start = (int) ($response->getPendingRequest()->query()->get('start') ?? 1);

        return $start + $this->perPage() - 1 >= (int) $pages['Total'];
    }

    protected function onRewind(): void
    {
        $this->lastPageFingerprint = null;
        $this->repeatedPage = false;
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

            // An endpoint that ignored start would answer the same page forever: a repeated page is the end. Two empty
            // pages in a row are not a repeat, as SmugMug can skip a whole page's worth of items.
            $fingerprint = sha1((string) json_encode($items));
            $this->repeatedPage = $items !== [] && $fingerprint === $this->lastPageFingerprint;
            $this->lastResponse = $response;
            $this->lastItems = $this->repeatedPage ? [] : $items;
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
