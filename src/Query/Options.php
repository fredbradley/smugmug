<?php

namespace FredBradley\SmugMug\Query;

use InvalidArgumentException;
use JsonException;

/**
 * The query string of a request: SmugMug's own options, such as _expand, _filter and paging, and the endpoint's
 * parameters, such as an image search's Text or a list's SortMethod.
 *
 *     Options::make()->expand('HighlightImage', 'Node.ChildNodes')->filter('Name', 'UrlPath')->count(50)
 *
 * Every request takes an Options or a plain array of the same parameters.
 */
final class Options
{
    /** @var array<string, mixed> */
    private array $parameters = [];

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function __construct(array $parameters = [])
    {
        $this->parameters = $parameters;
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public static function make(array $parameters = []): self
    {
        return new self($parameters);
    }

    /**
     * Options from what a request was given: an Options, an array, or nothing.
     *
     * @param  self|array<string, mixed>|null  $options
     */
    public static function of(self|array|null $options): self
    {
        return $options instanceof self ? clone $options : new self($options ?? []);
    }

    /**
     * Expand related objects into the response's Expansions, by the names in the object's Uris: "HighlightImage",
     * or a dotted path for one inside another, such as "Node.ChildNodes" (_expand).
     */
    public function expand(string ...$uris): self
    {
        return $this->mergeList('_expand', $uris);
    }

    /**
     * Return only these fields of each object (_filter).
     */
    public function filter(string ...$fields): self
    {
        return $this->mergeList('_filter', $fields);
    }

    /**
     * Return only these Uris of each object (_filteruri). With no names, every Uri is left out.
     */
    public function filterUri(string ...$uris): self
    {
        return $uris === [] ? $this->with('_filteruri', '') : $this->mergeList('_filteruri', $uris);
    }

    /**
     * Give each object's Uris as plain strings rather than objects (_shorturis).
     */
    public function shortUris(): self
    {
        return $this->with('_shorturis', '');
    }

    /**
     * How much SmugMug says besides the data: 1 (data only), 2 (the default, with the current method's Options) or
     * 3 (Options for every method) (_verbosity).
     */
    public function verbosity(int $level): self
    {
        if ($level < 1 || $level > 3) {
            throw new InvalidArgumentException('SmugMug verbosity is 1, 2 or 3.');
        }

        return $this->with('_verbosity', $level);
    }

    /**
     * The advanced form of the options (_config): a filter, filteruri and expand for each expansion, with args (the
     * query an expansion is fetched with) and multiargs. The array is sent as JSON, so
     *
     *     ->config(['expand' => ['ChildNodes' => ['args' => ['count' => 25]]]])
     *
     * fetches 25 children with each node.
     *
     * @param  array<string, mixed>  $config
     */
    public function config(array $config): self
    {
        try {
            return $this->with('_config', json_encode($config, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES));
        } catch (JsonException $exception) {
            throw new InvalidArgumentException('The _config option must be JSON-encodable: '.$exception->getMessage(), previous: $exception);
        }
    }

    /**
     * The 1-based position of the first item of a page (start).
     */
    public function start(int $start): self
    {
        if ($start < 1) {
            throw new InvalidArgumentException('SmugMug pages start at 1.');
        }

        return $this->with('start', $start);
    }

    /**
     * How many items a page has (count). SmugMug may give fewer: each endpoint has its own maximum.
     */
    public function count(int $count): self
    {
        if ($count < 1) {
            throw new InvalidArgumentException('A page needs at least one item.');
        }

        return $this->with('count', $count);
    }

    /**
     * One page: the given page number (from 1) of $perPage items.
     */
    public function page(int $page, int $perPage): self
    {
        return $this->count($perPage)->start(($page - 1) * $perPage + 1);
    }

    /**
     * Any other query parameter the endpoint documents, such as Text, SortMethod or MediaType. A list is sent
     * comma-separated; true and false as SmugMug's "true" and "false"; null removes the parameter.
     */
    public function with(string $name, mixed $value): self
    {
        $options = clone $this;

        if ($value === null) {
            unset($options->parameters[$name]);
        } else {
            $options->parameters[$name] = $value;
        }

        return $options;
    }

    /**
     * Several parameters at once.
     *
     * @param  array<string, mixed>  $parameters
     */
    public function withMany(array $parameters): self
    {
        $options = $this;

        foreach ($parameters as $name => $value) {
            $options = $options->with($name, $value);
        }

        return $options;
    }

    /**
     * These options with another's on top: the other's values win, and the names to _expand, _filter and
     * _filteruri are combined.
     *
     * @param  self|array<string, mixed>  $other
     */
    public function merge(self|array $other): self
    {
        $other = self::of($other);
        $options = $this;

        foreach ($other->parameters as $name => $value) {
            $options = in_array($name, ['_expand', '_filter', '_filteruri'], true) && is_array($value) && is_array($options->parameters[$name] ?? null)
                ? $options->mergeList($name, $value)
                : $options->with($name, $value);
        }

        return $options;
    }

    /**
     * The parameters as given, before they are written for the query string.
     *
     * @return array<string, mixed>
     */
    public function parameters(): array
    {
        return $this->parameters;
    }

    /**
     * A parameter's value, as it will be sent.
     */
    public function get(string $name): mixed
    {
        return $this->toArray()[$name] ?? null;
    }

    /**
     * The query string's parameters, as sent: lists comma-separated, booleans as "true" and "false".
     *
     * @return array<string, string|int|float>
     */
    public function toArray(): array
    {
        $query = [];

        foreach ($this->parameters as $name => $value) {
            $query[$name] = match (true) {
                is_bool($value) => $value ? 'true' : 'false',
                is_array($value) => implode(',', array_map(fn (mixed $item) => is_bool($item) ? ($item ? 'true' : 'false') : (string) $item, $value)),
                $value instanceof \BackedEnum => (string) $value->value,
                is_int($value), is_float($value) => $value,
                default => (string) $value,
            };
        }

        return $query;
    }

    /**
     * @param  list<mixed>  $values
     */
    private function mergeList(string $name, array $values): self
    {
        $existing = $this->parameters[$name] ?? [];
        $existing = is_array($existing) ? $existing : array_filter(explode(',', (string) $existing), fn (string $value) => $value !== '');

        return $this->with($name, array_values(array_unique([...$existing, ...$values])));
    }
}
