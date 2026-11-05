<?php

namespace FredBradley\SmugMug\Resources;

use FredBradley\SmugMug\Data\Commerce\Pricelist;
use FredBradley\SmugMug\Data\Data;
use FredBradley\SmugMug\Data\Design\PageDesign;
use FredBradley\SmugMug\Data\Image\Image;
use FredBradley\SmugMug\Data\Node\Node;
use FredBradley\SmugMug\Data\Social\Comment;
use FredBradley\SmugMug\Data\User\Grant;
use FredBradley\SmugMug\Listing;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\Node\CreateNodeClientDownloadRequest;
use FredBradley\SmugMug\Requests\Node\CreateNodeCommentRequest;
use FredBradley\SmugMug\Requests\Node\CreateNodeGrantRequest;
use FredBradley\SmugMug\Requests\Node\CreateNodeRequest;
use FredBradley\SmugMug\Requests\Node\CreateNodeSelectionRequest;
use FredBradley\SmugMug\Requests\Node\DeleteNodeRequest;
use FredBradley\SmugMug\Requests\Node\GetNodeChildrenRequest;
use FredBradley\SmugMug\Requests\Node\GetNodeCommentsRequest;
use FredBradley\SmugMug\Requests\Node\GetNodeCoverImageRequest;
use FredBradley\SmugMug\Requests\Node\GetNodeGrantsRequest;
use FredBradley\SmugMug\Requests\Node\GetNodeHighlightImageRequest;
use FredBradley\SmugMug\Requests\Node\GetNodePageDesignRequest;
use FredBradley\SmugMug\Requests\Node\GetNodeParentRequest;
use FredBradley\SmugMug\Requests\Node\GetNodeParentsRequest;
use FredBradley\SmugMug\Requests\Node\GetNodePricelistRequest;
use FredBradley\SmugMug\Requests\Node\GetNodeRequest;
use FredBradley\SmugMug\Requests\Node\GetNodesRequest;
use FredBradley\SmugMug\Requests\Node\MoveNodesRequest;
use FredBradley\SmugMug\Requests\Node\RemoveNodePricelistRequest;
use FredBradley\SmugMug\Requests\Node\SearchNodesRequest;
use FredBradley\SmugMug\Requests\Node\SetNodePricelistRequest;
use FredBradley\SmugMug\Requests\Node\UpdateNodeAlbumsRequest;
use FredBradley\SmugMug\Requests\Node\UpdateNodeRequest;

/**
 * Nodes: the folders, albums and pages of a site, by node id, from SmugMug::nodes().
 */
class NodeResource extends BaseResource
{
    /**
     * A node (folder, album or page), by id: GET /api/v2/node/{nodeId}.
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function get(string $nodeId, Options|array $options = []): ?Node
    {
        return $this->fetch(new GetNodeRequest($nodeId, $this->options($options)));
    }

    /**
     * As get(), but null when SmugMug has no such object or the credentials may not see it.
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function find(string $nodeId, Options|array $options = []): ?Node
    {
        return $this->fetchOrNull(new GetNodeRequest($nodeId, $this->options($options)));
    }

    /**
     * Several nodes at once (multi-get), by id: GET /api/v2/node/{nodeIds}.
     *
     * @param  list<string>|string  $nodeIds
     * @param  Options|array<string, mixed>  $options
     * @return Listing<Node>
     */
    public function many(string|array $nodeIds, Options|array $options = []): Listing
    {
        return $this->listing(fn (Options $o) => new GetNodesRequest($nodeIds, $o), $options);
    }

    /**
     * Change a node: PATCH /api/v2/node/{nodeId}.
     *
     *  - Name, UrlName, Description, Keywords (a list)
     *  - Privacy: Public, Unlisted or Private
     *  - SecurityType: None, Password or GrantAccess
     *  - Password, PasswordHint
     *  - SmugSearchable: No, Local, LocalUser, Yes or Inherit from User
     *  - WorldSearchable: No, HomeOnly, Yes or Inherit from User
     *  - SortMethod: SortIndex, Name, DateAdded or DateModified
     *  - SortDirection: Ascending or Descending
     *  - HighlightImageUri, CoverImageUri, ShowCoverImage: the images that represent it
     *  - HideOwner
     *  - AlbumTemplateUri
     *  - AutoRename: rename instead of failing when the UrlName is taken
     *  - ResetUrlName: make the UrlName from the Name again
     *
     * @param  array<string, mixed>|Data  $fields
     * @param  Options|array<string, mixed>  $options
     */
    public function update(string $nodeId, array|Data $fields, Options|array $options = []): ?Node
    {
        return $this->fetch(new UpdateNodeRequest($nodeId, $fields, $this->options($options)));
    }

    /**
     * Delete a node and everything in it (to the trash): DELETE /api/v2/node/{nodeId}.
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function delete(string $nodeId, Options|array $options = []): bool
    {
        return $this->perform(new DeleteNodeRequest($nodeId, $this->options($options)));
    }

    /**
     * The folders, albums and pages in a folder node: GET /api/v2/node/{nodeId}!children.
     *
     *  - Type: All, Folder Album Page, Folder, Album, Page or System Album
     *  - SortMethod: DateAdded, DateModified, Name or Organizer
     *  - SortDirection: Ascending or Descending
     *
     * @param  Options|array<string, mixed>  $options
     * @return Listing<Node>
     */
    public function children(string $nodeId, Options|array $options = []): Listing
    {
        return $this->listing(fn (Options $o) => new GetNodeChildrenRequest($nodeId, $o), $options);
    }

    /**
     * Create a folder, album or page in a folder node: POST /api/v2/node/{nodeId}!children.
     *
     *  - Type (required): Album, Folder or Page
     *  - Name (required)
     *  - UrlName: the address part (made from the Name when left out)
     *  - Description, Keywords (a list)
     *  - Privacy: Public, Unlisted or Private
     *  - SecurityType, Password, PasswordHint
     *  - SmugSearchable, WorldSearchable
     *  - SortMethod, SortDirection
     *  - HighlightImageUri, CoverImageUri, ShowCoverImage, HideOwner
     *  - AlbumTemplateUri: an album template to create an album from
     *  - AutoRename: rename instead of failing when the UrlName is taken
     *
     * @param  array<string, mixed>|Data  $fields
     * @param  Options|array<string, mixed>  $options
     */
    public function create(string $nodeId, array|Data $fields, Options|array $options = []): ?Node
    {
        return $this->fetch(new CreateNodeRequest($nodeId, $fields, $this->options($options)));
    }

    /**
     * The folder node a node is in: GET /api/v2/node/{nodeId}!parent.
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function parent(string $nodeId, Options|array $options = []): ?Node
    {
        return $this->fetch(new GetNodeParentRequest($nodeId, $this->options($options)));
    }

    /**
     * Every folder node above a node, nearest first: GET /api/v2/node/{nodeId}!parents.
     *
     * @param  Options|array<string, mixed>  $options
     * @return Listing<Node>
     */
    public function parents(string $nodeId, Options|array $options = []): Listing
    {
        return $this->listing(fn (Options $o) => new GetNodeParentsRequest($nodeId, $o), $options);
    }

    /**
     * A node's cover image: GET /api/v2/node/{nodeId}!cover.
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function cover(string $nodeId, Options|array $options = []): ?Image
    {
        return $this->fetch(new GetNodeCoverImageRequest($nodeId, $this->options($options)));
    }

    /**
     * The image that represents a node: GET /api/v2/highlight/node/{nodeId}.
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function highlightImage(string $nodeId, Options|array $options = []): ?Image
    {
        return $this->fetch(new GetNodeHighlightImageRequest($nodeId, $this->options($options)));
    }

    /**
     * The comments on a node: GET /api/v2/node/{nodeId}!comments.
     *
     * @param  Options|array<string, mixed>  $options
     * @return Listing<Comment>
     */
    public function comments(string $nodeId, Options|array $options = []): Listing
    {
        return $this->listing(fn (Options $o) => new GetNodeCommentsRequest($nodeId, $o), $options);
    }

    /**
     * Comment on a node: POST /api/v2/node/{nodeId}!comments.
     *
     *  - Comment (required): the text
     *  - Name, Email, Link: who is commenting, for a comment without a token
     *  - Rating: a rating
     *  - SocialID, ServiceID: a social sign-in
     *
     * @param  array<string, mixed>|Data  $fields
     * @param  Options|array<string, mixed>  $options
     */
    public function addComment(string $nodeId, array|Data $fields, Options|array $options = []): ?Comment
    {
        return $this->fetch(new CreateNodeCommentRequest($nodeId, $fields, $this->options($options)));
    }

    /**
     * Who has been granted access to a node: GET /api/v2/node/{nodeId}!grants.
     *
     * @param  Options|array<string, mixed>  $options
     * @return Listing<Grant>
     */
    public function grants(string $nodeId, Options|array $options = []): Listing
    {
        return $this->listing(fn (Options $o) => new GetNodeGrantsRequest($nodeId, $o), $options);
    }

    /**
     * Grant someone access to a node: POST /api/v2/node/{nodeId}!grants.
     *
     *  - ContactUri: the contact to grant access to
     *  - Email: or an email address
     *
     * @param  array<string, mixed>|Data  $fields
     * @param  Options|array<string, mixed>  $options
     */
    public function grant(string $nodeId, array|Data $fields, Options|array $options = []): ?Grant
    {
        return $this->fetch(new CreateNodeGrantRequest($nodeId, $fields, $this->options($options)));
    }

    /**
     * The pricelist mapped to a node: GET /api/v2/node/{nodeId}!pricelist.
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function pricelist(string $nodeId, Options|array $options = []): ?Pricelist
    {
        return $this->fetch(new GetNodePricelistRequest($nodeId, $this->options($options)));
    }

    /**
     * Map a pricelist to a node: POST /api/v2/node/{nodeId}!pricelist.
     *
     *  - PricelistUri (required): the pricelist to map
     *
     * @param  array<string, mixed>|Data  $fields
     * @param  Options|array<string, mixed>  $options
     */
    public function setPricelist(string $nodeId, array|Data $fields, Options|array $options = []): ?Pricelist
    {
        return $this->fetch(new SetNodePricelistRequest($nodeId, $fields, $this->options($options)));
    }

    /**
     * Remove the pricelist mapped to a node: DELETE /api/v2/node/{nodeId}!pricelist.
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function removePricelist(string $nodeId, Options|array $options = []): bool
    {
        return $this->perform(new RemoveNodePricelistRequest($nodeId, $this->options($options)));
    }

    /**
     * The page design of a node: GET /api/v2/node/{nodeId}!nodedesign.
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function design(string $nodeId, Options|array $options = []): ?PageDesign
    {
        return $this->fetch(new GetNodePageDesignRequest($nodeId, $this->options($options)));
    }

    /**
     * Create a selection (an album of chosen images) under a node: POST /api/v2/node/{nodeId}!createselection.
     *
     *  - AlbumImageUris: the album images to select
     *  - SearchText, SearchSort: or a search
     *  - Limit: the most images to select
     *
     * @param  array<string, mixed>  $parameters
     * @param  Options|array<string, mixed>  $options
     * @return array<string, mixed>
     */
    public function createSelection(string $nodeId, array $parameters = [], Options|array $options = []): array
    {
        return $this->fetch(new CreateNodeSelectionRequest($nodeId, $parameters, $this->options($options)));
    }

    /**
     * Create a client download for a node: POST /api/v2/node/{nodeId}!createclientdownload.
     *
     *  - Limit: the most images to include
     *
     * @param  array<string, mixed>  $parameters
     * @param  Options|array<string, mixed>  $options
     * @return array<string, mixed>
     */
    public function createClientDownload(string $nodeId, array $parameters = [], Options|array $options = []): array
    {
        return $this->fetch(new CreateNodeClientDownloadRequest($nodeId, $parameters, $this->options($options)));
    }

    /**
     * Move nodes into a folder node: POST /api/v2/node/{nodeId}!movenodes.
     *
     *  - MoveUris (required): the nodes to move into this folder node (a list of URIs)
     *  - AutoRename: rename instead of failing on a clash
     *  - Async: true to do it in the background
     *
     * @param  array<string, mixed>  $parameters
     * @param  Options|array<string, mixed>  $options
     * @return array<string, mixed>
     */
    public function move(string $nodeId, array $parameters = [], Options|array $options = []): array
    {
        return $this->fetch(new MoveNodesRequest($nodeId, $parameters, $this->options($options)));
    }

    /**
     * Search nodes: GET /api/v2/node!search.
     *
     *  - Scope (required): the URI of the user or node to search in
     *  - Text: the text to search for
     *  - SortMethod: Rank or LastUpdated
     *  - SortDirection: Ascending or Descending
     *
     * @param  Options|array<string, mixed>  $options
     * @return Listing<Node>
     */
    public function search(Options|array $options = []): Listing
    {
        return $this->listing(fn (Options $o) => new SearchNodesRequest($o), $options);
    }

    /**
     * Change the settings of several albums at once: POST /api/v2/node!updatealbums.
     *
     *  - NodeUris (required): the album nodes to change (a list of URIs)
     *  - any album setting (Privacy, Watermark, AllowDownloads, LargestSize, Printable, PricelistUri and so on): the value to give every one
     *
     * @param  array<string, mixed>  $parameters
     * @param  Options|array<string, mixed>  $options
     * @return array<string, mixed>
     */
    public function updateAlbums(array $parameters = [], Options|array $options = []): array
    {
        return $this->fetch(new UpdateNodeAlbumsRequest($parameters, $this->options($options)));
    }
}
