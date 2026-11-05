<?php

namespace FredBradley\SmugMug\Resources;

use FredBradley\SmugMug\Data\Album\Album;
use FredBradley\SmugMug\Data\Album\AlbumListItem;
use FredBradley\SmugMug\Data\Data;
use FredBradley\SmugMug\Data\Image\Image;
use FredBradley\SmugMug\Data\Node\Folder;
use FredBradley\SmugMug\Data\Node\FolderListItem;
use FredBradley\SmugMug\Data\Node\Page;
use FredBradley\SmugMug\Data\Node\Size;
use FredBradley\SmugMug\Data\User\Grant;
use FredBradley\SmugMug\Listing;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\Folder\CreateFolderAlbumFromTemplateRequest;
use FredBradley\SmugMug\Requests\Folder\CreateFolderAlbumRequest;
use FredBradley\SmugMug\Requests\Folder\CreateFolderGrantRequest;
use FredBradley\SmugMug\Requests\Folder\CreateFolderPageRequest;
use FredBradley\SmugMug\Requests\Folder\CreateFolderRequest;
use FredBradley\SmugMug\Requests\Folder\DeleteFolderRequest;
use FredBradley\SmugMug\Requests\Folder\GetFolderAlbumListRequest;
use FredBradley\SmugMug\Requests\Folder\GetFolderAlbumsRequest;
use FredBradley\SmugMug\Requests\Folder\GetFolderByIdRequest;
use FredBradley\SmugMug\Requests\Folder\GetFolderFolderListRequest;
use FredBradley\SmugMug\Requests\Folder\GetFolderFoldersRequest;
use FredBradley\SmugMug\Requests\Folder\GetFolderGrantsRequest;
use FredBradley\SmugMug\Requests\Folder\GetFolderHighlightImageRequest;
use FredBradley\SmugMug\Requests\Folder\GetFolderPagesRequest;
use FredBradley\SmugMug\Requests\Folder\GetFolderParentRequest;
use FredBradley\SmugMug\Requests\Folder\GetFolderParentsRequest;
use FredBradley\SmugMug\Requests\Folder\GetFolderRequest;
use FredBradley\SmugMug\Requests\Folder\GetFolderSizeRequest;
use FredBradley\SmugMug\Requests\Folder\MoveFolderAlbumsRequest;
use FredBradley\SmugMug\Requests\Folder\MoveFolderPagesRequest;
use FredBradley\SmugMug\Requests\Folder\MoveFoldersRequest;
use FredBradley\SmugMug\Requests\Folder\SearchFolderRequest;
use FredBradley\SmugMug\Requests\Folder\SortFolderAlbumsRequest;
use FredBradley\SmugMug\Requests\Folder\SortFolderPagesRequest;
use FredBradley\SmugMug\Requests\Folder\SortFoldersRequest;
use FredBradley\SmugMug\Requests\Folder\UpdateFolderRequest;

/**
 * Folders by path (the folder API, which nodes replace), from SmugMug::folders().
 */
class FolderResource extends BaseResource
{
    /**
     * A folder, by user and path ('' for the root): GET /api/v2/folder/user/{nickname}{path*}.
     *
     *  - FolderID: the folder's id instead of its path
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function get(string $nickname, string $path, Options|array $options = []): ?Folder
    {
        return $this->fetch(new GetFolderRequest($nickname, $path, $this->options($options)));
    }

    /**
     * As get(), but null when SmugMug has no such object or the credentials may not see it.
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function find(string $nickname, string $path, Options|array $options = []): ?Folder
    {
        return $this->fetchOrNull(new GetFolderRequest($nickname, $path, $this->options($options)));
    }

    /**
     * Change a folder: PATCH /api/v2/folder/user/{nickname}{path*}.
     *
     *  - Name, UrlName, Description, Keywords
     *  - Privacy: Public, Unlisted or Private
     *  - SecurityType, Password, PasswordHint
     *  - SmugSearchable, WorldSearchable
     *  - SortMethod: SortIndex, Name, DateAdded or DateModified
     *  - SortDirection
     *  - HighlightImageUri
     *  - AutoRename
     *
     * @param  array<string, mixed>|Data  $fields
     * @param  Options|array<string, mixed>  $options
     */
    public function update(string $nickname, string $path, array|Data $fields, Options|array $options = []): ?Folder
    {
        return $this->fetch(new UpdateFolderRequest($nickname, $path, $fields, $this->options($options)));
    }

    /**
     * Delete a folder and everything in it: DELETE /api/v2/folder/user/{nickname}{path*}.
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function delete(string $nickname, string $path, Options|array $options = []): bool
    {
        return $this->perform(new DeleteFolderRequest($nickname, $path, $this->options($options)));
    }

    /**
     * A folder, by its node id: GET /api/v2/folder/id/{nodeId}.
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function byId(string $nodeId, Options|array $options = []): ?Folder
    {
        return $this->fetch(new GetFolderByIdRequest($nodeId, $this->options($options)));
    }

    /**
     * The albums in a folder: GET /api/v2/folder/user/{nickname}{path*}!albums.
     *
     *  - FolderID
     *
     * @param  Options|array<string, mixed>  $options
     * @return Listing<Album>
     */
    public function albums(string $nickname, string $path, Options|array $options = []): Listing
    {
        return $this->listing(fn (Options $o) => new GetFolderAlbumsRequest($nickname, $path, $o), $options);
    }

    /**
     * Create an album in a folder: POST /api/v2/folder/user/{nickname}{path*}!albums.
     *
     *  - Name (required), UrlName (required)
     *  - and any album setting (Privacy, SecurityType, Description, Keywords, SortMethod, Watermark and so on)
     *  - AlbumTemplateUri: an album template to start from
     *  - AutoRename
     *
     * @param  array<string, mixed>|Data  $fields
     * @param  Options|array<string, mixed>  $options
     */
    public function createAlbum(string $nickname, string $path, array|Data $fields, Options|array $options = []): ?Album
    {
        return $this->fetch(new CreateFolderAlbumRequest($nickname, $path, $fields, $this->options($options)));
    }

    /**
     * Every album under a folder, as a flat list: GET /api/v2/folder/user/{nickname}{path*}!albumlist.
     *
     *  - FolderID
     *
     * @param  Options|array<string, mixed>  $options
     * @return Listing<AlbumListItem>
     */
    public function albumList(string $nickname, string $path, Options|array $options = []): Listing
    {
        return $this->listing(fn (Options $o) => new GetFolderAlbumListRequest($nickname, $path, $o), $options);
    }

    /**
     * The folders in a folder: GET /api/v2/folder/user/{nickname}{path*}!folders.
     *
     *  - FolderID
     *
     * @param  Options|array<string, mixed>  $options
     * @return Listing<Folder>
     */
    public function folders(string $nickname, string $path, Options|array $options = []): Listing
    {
        return $this->listing(fn (Options $o) => new GetFolderFoldersRequest($nickname, $path, $o), $options);
    }

    /**
     * Create a folder in a folder: POST /api/v2/folder/user/{nickname}{path*}!folders.
     *
     *  - Name (required), UrlName (required)
     *  - Description, Keywords
     *  - Privacy, SecurityType, Password, PasswordHint
     *  - SmugSearchable, WorldSearchable
     *  - SortMethod, SortDirection
     *  - HighlightImageUri
     *  - AutoRename
     *
     * @param  array<string, mixed>|Data  $fields
     * @param  Options|array<string, mixed>  $options
     */
    public function createFolder(string $nickname, string $path, array|Data $fields, Options|array $options = []): ?Folder
    {
        return $this->fetch(new CreateFolderRequest($nickname, $path, $fields, $this->options($options)));
    }

    /**
     * Every folder under a folder, as a flat list: GET /api/v2/folder/user/{nickname}{path*}!folderlist.
     *
     *  - FolderID
     *
     * @param  Options|array<string, mixed>  $options
     * @return Listing<FolderListItem>
     */
    public function folderList(string $nickname, string $path, Options|array $options = []): Listing
    {
        return $this->listing(fn (Options $o) => new GetFolderFolderListRequest($nickname, $path, $o), $options);
    }

    /**
     * The pages in a folder: GET /api/v2/folder/user/{nickname}{path*}!pages.
     *
     *  - FolderID
     *
     * @param  Options|array<string, mixed>  $options
     * @return Listing<Page>
     */
    public function pages(string $nickname, string $path, Options|array $options = []): Listing
    {
        return $this->listing(fn (Options $o) => new GetFolderPagesRequest($nickname, $path, $o), $options);
    }

    /**
     * Create a page in a folder: POST /api/v2/folder/user/{nickname}{path*}!pages.
     *
     *  - Name (required), UrlName (required)
     *  - Privacy, SecurityType, Password, PasswordHint
     *  - AutoRename
     *
     * @param  array<string, mixed>|Data  $fields
     * @param  Options|array<string, mixed>  $options
     */
    public function createPage(string $nickname, string $path, array|Data $fields, Options|array $options = []): ?Page
    {
        return $this->fetch(new CreateFolderPageRequest($nickname, $path, $fields, $this->options($options)));
    }

    /**
     * Search the folders under a folder: GET /api/v2/folder/user/{nickname}{path*}!search.
     *
     *  - Text: the text to search for
     *  - SortMethod: Rank or LastUpdated
     *  - SortDirection: Ascending or Descending
     *
     * @param  Options|array<string, mixed>  $options
     * @return Listing<Folder>
     */
    public function search(string $nickname, string $path, Options|array $options = []): Listing
    {
        return $this->listing(fn (Options $o) => new SearchFolderRequest($nickname, $path, $o), $options);
    }

    /**
     * The image that represents a folder: GET /api/v2/folder/user/{nickname}{path*}!highlightimage.
     *
     *  - FolderID
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function highlightImage(string $nickname, string $path, Options|array $options = []): ?Image
    {
        return $this->fetch(new GetFolderHighlightImageRequest($nickname, $path, $this->options($options)));
    }

    /**
     * The folder a folder is in: GET /api/v2/folder/user/{nickname}{path*}!parent.
     *
     *  - FolderID
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function parent(string $nickname, string $path, Options|array $options = []): ?Folder
    {
        return $this->fetch(new GetFolderParentRequest($nickname, $path, $this->options($options)));
    }

    /**
     * Every folder above a folder: GET /api/v2/folder/user/{nickname}{path*}!parents.
     *
     *  - FolderID
     *
     * @param  Options|array<string, mixed>  $options
     * @return Listing<Folder>
     */
    public function parents(string $nickname, string $path, Options|array $options = []): Listing
    {
        return $this->listing(fn (Options $o) => new GetFolderParentsRequest($nickname, $path, $o), $options);
    }

    /**
     * How much a folder holds: GET /api/v2/folder/user/{nickname}{path*}!size.
     *
     *  - FolderID
     *  - depth: 1 (this folder) or MAX (everything under it)
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function size(string $nickname, string $path, Options|array $options = []): ?Size
    {
        return $this->fetch(new GetFolderSizeRequest($nickname, $path, $this->options($options)));
    }

    /**
     * Who has been granted access to a folder: GET /api/v2/folder/user/{nickname}{path*}!grants.
     *
     * @param  Options|array<string, mixed>  $options
     * @return Listing<Grant>
     */
    public function grants(string $nickname, string $path, Options|array $options = []): Listing
    {
        return $this->listing(fn (Options $o) => new GetFolderGrantsRequest($nickname, $path, $o), $options);
    }

    /**
     * Grant someone access to a folder: POST /api/v2/folder/user/{nickname}{path*}!grants.
     *
     *  - ContactUri: the contact to grant access to
     *  - Email: or an email address
     *
     * @param  array<string, mixed>|Data  $fields
     * @param  Options|array<string, mixed>  $options
     */
    public function grant(string $nickname, string $path, array|Data $fields, Options|array $options = []): ?Grant
    {
        return $this->fetch(new CreateFolderGrantRequest($nickname, $path, $fields, $this->options($options)));
    }

    /**
     * Move albums into a folder: POST /api/v2/folder/user/{nickname}{path*}!movealbums.
     *
     *  - MoveUris (required): the albums to move here (a list of URIs)
     *  - AutoRename
     *
     * @param  array<string, mixed>  $parameters
     * @param  Options|array<string, mixed>  $options
     * @return array<string, mixed>
     */
    public function moveAlbums(string $nickname, string $path, array $parameters = [], Options|array $options = []): array
    {
        return $this->fetch(new MoveFolderAlbumsRequest($nickname, $path, $parameters, $this->options($options)));
    }

    /**
     * Move folders into a folder: POST /api/v2/folder/user/{nickname}{path*}!movefolders.
     *
     *  - MoveUris (required): the folders to move here (a list of URIs)
     *  - AutoRename
     *
     * @param  array<string, mixed>  $parameters
     * @param  Options|array<string, mixed>  $options
     * @return array<string, mixed>
     */
    public function moveFolders(string $nickname, string $path, array $parameters = [], Options|array $options = []): array
    {
        return $this->fetch(new MoveFoldersRequest($nickname, $path, $parameters, $this->options($options)));
    }

    /**
     * Move pages into a folder: POST /api/v2/folder/user/{nickname}{path*}!movepages.
     *
     *  - MoveUris (required): the pages to move here (a list of URIs)
     *  - AutoRename
     *
     * @param  array<string, mixed>  $parameters
     * @param  Options|array<string, mixed>  $options
     * @return array<string, mixed>
     */
    public function movePages(string $nickname, string $path, array $parameters = [], Options|array $options = []): array
    {
        return $this->fetch(new MoveFolderPagesRequest($nickname, $path, $parameters, $this->options($options)));
    }

    /**
     * Reorder a folder's albums: POST /api/v2/folder/user/{nickname}{path*}!sortalbums.
     *
     *  - MoveUris (required): the albums to move (a list of URIs)
     *  - MoveLocation (required): Before or After
     *  - Uri (required): the album to move them before or after
     *
     * @param  array<string, mixed>  $parameters
     * @param  Options|array<string, mixed>  $options
     * @return array<string, mixed>
     */
    public function sortAlbums(string $nickname, string $path, array $parameters = [], Options|array $options = []): array
    {
        return $this->fetch(new SortFolderAlbumsRequest($nickname, $path, $parameters, $this->options($options)));
    }

    /**
     * Reorder a folder's folders: POST /api/v2/folder/user/{nickname}{path*}!sortfolders.
     *
     *  - MoveUris (required): the folders to move (a list of URIs)
     *  - MoveLocation (required): Before or After
     *  - Uri (required): the folder to move them before or after
     *
     * @param  array<string, mixed>  $parameters
     * @param  Options|array<string, mixed>  $options
     * @return array<string, mixed>
     */
    public function sortFolders(string $nickname, string $path, array $parameters = [], Options|array $options = []): array
    {
        return $this->fetch(new SortFoldersRequest($nickname, $path, $parameters, $this->options($options)));
    }

    /**
     * Reorder a folder's pages: POST /api/v2/folder/user/{nickname}{path*}!sortpages.
     *
     *  - MoveUris (required): the pages to move (a list of URIs)
     *  - MoveLocation (required): Before or After
     *  - Uri (required): the page to move them before or after
     *
     * @param  array<string, mixed>  $parameters
     * @param  Options|array<string, mixed>  $options
     * @return array<string, mixed>
     */
    public function sortPages(string $nickname, string $path, array $parameters = [], Options|array $options = []): array
    {
        return $this->fetch(new SortFolderPagesRequest($nickname, $path, $parameters, $this->options($options)));
    }

    /**
     * Create an album in a folder from an album template: POST /api/v2/folder/user/{nickname}{path*}!albumfromalbumtemplate.
     *
     *  - AlbumTemplateUri (required): the template
     *  - Name, UrlName, Description, Keywords, DownloadPassword, SecurityType, ThemeUri, UploadKey
     *  - AutoRename
     *
     * @param  array<string, mixed>|Data  $fields
     * @param  Options|array<string, mixed>  $options
     */
    public function createAlbumFromTemplate(string $nickname, string $path, array|Data $fields, Options|array $options = []): ?Album
    {
        return $this->fetch(new CreateFolderAlbumFromTemplateRequest($nickname, $path, $fields, $this->options($options)));
    }
}
