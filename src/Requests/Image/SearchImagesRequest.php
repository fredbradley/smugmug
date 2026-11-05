<?php

namespace FredBradley\SmugMug\Requests\Image;

use FredBradley\SmugMug\Data\Image\Image;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ListRequest;

/**
 * Search images: GET /api/v2/image!search.
 *
 * Parameters (in the options):
 *  - Scope (required): the URI of the user, node or album to search in
 *  - Text: the text to search for
 *  - Keywords: only images with these keywords
 *  - SortMethod: DateTaken, DateUploaded or Popular
 *  - SortDirection: Ascending or Descending
 *  - Relevance: sort by relevance first
 *  - DateTakenStart, DateTakenEnd, DateUploadedStart, DateUploadedEnd: ISO 8601 dates
 *  - Type: Image or Video
 */
class SearchImagesRequest extends ListRequest
{
    protected ?string $dto = Image::class;

    /**
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(Options|array $options = [])
    {
        $this->setOptions($options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/image!search';
    }
}
