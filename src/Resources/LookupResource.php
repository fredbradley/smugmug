<?php

namespace FredBradley\SmugMug\Resources;

use FredBradley\SmugMug\Data\Node\Node;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\Lookup\LookupNicknameUrlPathRequest;
use FredBradley\SmugMug\Requests\Lookup\LookupOldNodeIdRequest;
use FredBradley\SmugMug\Requests\Lookup\LookupWebUriRequest;

/**
 * Finding a folder, album, page or image by its address, from SmugMug::lookup().
 */
class LookupResource extends BaseResource
{
    /**
     * The folder, album or page at a user's path: GET /api/v2!nicknameurlpathlookup.
     *
     *  - nickname (required): the user
     *  - urlpath (required): the path below their site
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function urlPath(Options|array $options = []): mixed
    {
        return $this->fetch(new LookupNicknameUrlPathRequest($this->options($options)));
    }

    /**
     * The folder, album, page or album image a SmugMug web address shows: GET /api/v2!weburilookup.
     *
     *  - WebUri (required): the address of a page of a SmugMug site
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function webUri(Options|array $options = []): mixed
    {
        return $this->fetch(new LookupWebUriRequest($this->options($options)));
    }

    /**
     * The node that a legacy (pre-node) id became: GET /api/v2/node!oldidlookup.
     *
     *  - ID (required): the numeric id of the legacy category or subcategory
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function oldNodeId(Options|array $options = []): ?Node
    {
        return $this->fetch(new LookupOldNodeIdRequest($this->options($options)));
    }
}
