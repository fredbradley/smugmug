<?php

namespace FredBradley\SmugMug\Requests\Node;

use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ActionRequest;
use JsonSerializable;

/**
 * Change the settings of several albums at once: POST /api/v2/node!updatealbums.
 *
 * Parameters (in the body):
 *  - NodeUris (required): the album nodes to change (a list of URIs)
 *  - any album setting (Privacy, Watermark, AllowDownloads, LargestSize, Printable, PricelistUri and so on): the value to give every one
 */
class UpdateNodeAlbumsRequest extends ActionRequest
{
    /**
     * @param  array<mixed>|JsonSerializable  $body  SmugMug's field names and values, or a model.
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(array|JsonSerializable $body = [], Options|array $options = [])
    {
        $this->setBody($body, $options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/node!updatealbums';
    }
}
