<?php

namespace FredBradley\SmugMug\Requests\Album;

use FredBradley\SmugMug\Data\Album\Album;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ListRequest;

/**
 * Search albums: GET /api/v2/album!search.
 *
 * Parameters (in the options):
 *  - Scope (required): the URI of the user or node to search in
 *  - Text: the text to search for
 *  - SortMethod: Rank or LastUpdated
 *  - SortDirection: Ascending or Descending
 */
class SearchAlbumsRequest extends ListRequest
{
    protected ?string $dto = Album::class;

    /**
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(Options|array $options = [])
    {
        $this->setOptions($options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/album!search';
    }
}
