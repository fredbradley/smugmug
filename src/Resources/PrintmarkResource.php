<?php

namespace FredBradley\SmugMug\Resources;

use FredBradley\SmugMug\Data\Data;
use FredBradley\SmugMug\Data\Social\Printmark;
use FredBradley\SmugMug\Listing;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\Printmark\CreatePrintmarkRequest;
use FredBradley\SmugMug\Requests\Printmark\GetPrintmarkRequest;
use FredBradley\SmugMug\Requests\Printmark\GetUserPrintmarksRequest;

/**
 * Printmarks, from SmugMug::printmarks().
 */
class PrintmarkResource extends BaseResource
{
    /**
     * A user's printmarks: GET /api/v2/user/{nickname}!printmarks.
     *
     *  - Unavailable: include the printmarks the plan no longer allows
     *
     * @param  Options|array<string, mixed>  $options
     * @return Listing<Printmark>
     */
    public function forUser(string $nickname, Options|array $options = []): Listing
    {
        return $this->listing(fn (Options $o) => new GetUserPrintmarksRequest($nickname, $o), $options);
    }

    /**
     * Create a printmark: POST /api/v2/printmark.
     *
     *  - Name
     *  - ImageUri, ImageID or ImageKey: the image to print
     *  - Dissolve: opacity
     *  - Gap: the margin
     *  - Location: where on the print
     *  - Scale: its size
     *
     * @param  array<string, mixed>|Data  $fields
     * @param  Options|array<string, mixed>  $options
     */
    public function create(array|Data $fields, Options|array $options = []): ?Printmark
    {
        return $this->fetch(new CreatePrintmarkRequest($fields, $this->options($options)));
    }

    /**
     * A printmark, by id: GET /api/v2/printmark/{printmarkId}.
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function get(string $printmarkId, Options|array $options = []): ?Printmark
    {
        return $this->fetch(new GetPrintmarkRequest($printmarkId, $this->options($options)));
    }
}
