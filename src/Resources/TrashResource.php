<?php

namespace FredBradley\SmugMug\Resources;

use FredBradley\SmugMug\Data\Data;
use FredBradley\SmugMug\Data\Node\DeletedNode;
use FredBradley\SmugMug\Data\Node\Recovered;
use FredBradley\SmugMug\Listing;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\Trash\GetDeletedAlbumRequest;
use FredBradley\SmugMug\Requests\Trash\GetDeletedFolderRequest;
use FredBradley\SmugMug\Requests\Trash\GetDeletedPageRequest;
use FredBradley\SmugMug\Requests\Trash\GetUserDeletedAlbumsRequest;
use FredBradley\SmugMug\Requests\Trash\GetUserDeletedFoldersRequest;
use FredBradley\SmugMug\Requests\Trash\GetUserDeletedPagesRequest;
use FredBradley\SmugMug\Requests\Trash\RecoverDeletedAlbumRequest;
use FredBradley\SmugMug\Requests\Trash\RecoverDeletedFolderRequest;
use FredBradley\SmugMug\Requests\Trash\RecoverDeletedPageRequest;

/**
 * Deleted albums, folders and pages, and recovering them, from SmugMug::trash().
 */
class TrashResource extends BaseResource
{
    /**
     * The albums in a user's trash: GET /api/v2/user/{nickname}!deletedalbums.
     *
     * @param  Options|array<string, mixed>  $options
     * @return Listing<DeletedNode>
     */
    public function albums(string $nickname, Options|array $options = []): Listing
    {
        return $this->listing(fn (Options $o) => new GetUserDeletedAlbumsRequest($nickname, $o), $options);
    }

    /**
     * The folders in a user's trash: GET /api/v2/user/{nickname}!deletedfolders.
     *
     * @param  Options|array<string, mixed>  $options
     * @return Listing<DeletedNode>
     */
    public function folders(string $nickname, Options|array $options = []): Listing
    {
        return $this->listing(fn (Options $o) => new GetUserDeletedFoldersRequest($nickname, $o), $options);
    }

    /**
     * The pages in a user's trash: GET /api/v2/user/{nickname}!deletedpages.
     *
     * @param  Options|array<string, mixed>  $options
     * @return Listing<DeletedNode>
     */
    public function pages(string $nickname, Options|array $options = []): Listing
    {
        return $this->listing(fn (Options $o) => new GetUserDeletedPagesRequest($nickname, $o), $options);
    }

    /**
     * A deleted album, by node id: GET /api/v2/deleted/album/{nodeId}.
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function album(string $nodeId, Options|array $options = []): ?DeletedNode
    {
        return $this->fetch(new GetDeletedAlbumRequest($nodeId, $this->options($options)));
    }

    /**
     * A deleted folder, by node id: GET /api/v2/deleted/folder/{nodeId}.
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function folder(string $nodeId, Options|array $options = []): ?DeletedNode
    {
        return $this->fetch(new GetDeletedFolderRequest($nodeId, $this->options($options)));
    }

    /**
     * A deleted page, by node id: GET /api/v2/deleted/page/{nodeId}.
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function page(string $nodeId, Options|array $options = []): ?DeletedNode
    {
        return $this->fetch(new GetDeletedPageRequest($nodeId, $this->options($options)));
    }

    /**
     * Recover a deleted album: POST /api/v2/deleted/album/{nodeId}!recover.
     *
     *  - FolderUri: the folder to recover it into (where it was by default)
     *  - IncludeChildren: recover its images too
     *
     * @param  array<string, mixed>|Data  $fields
     * @param  Options|array<string, mixed>  $options
     */
    public function recoverAlbum(string $nodeId, array|Data $fields, Options|array $options = []): ?Recovered
    {
        return $this->fetch(new RecoverDeletedAlbumRequest($nodeId, $fields, $this->options($options)));
    }

    /**
     * Recover a deleted folder: POST /api/v2/deleted/folder/{nodeId}!recover.
     *
     *  - FolderUri: the folder to recover it into
     *  - IncludeChildren: recover what was in it too
     *
     * @param  array<string, mixed>|Data  $fields
     * @param  Options|array<string, mixed>  $options
     */
    public function recoverFolder(string $nodeId, array|Data $fields, Options|array $options = []): ?Recovered
    {
        return $this->fetch(new RecoverDeletedFolderRequest($nodeId, $fields, $this->options($options)));
    }

    /**
     * Recover a deleted page: POST /api/v2/deleted/page/{nodeId}!recover.
     *
     *  - FolderUri: the folder to recover it into
     *
     * @param  array<string, mixed>|Data  $fields
     * @param  Options|array<string, mixed>  $options
     */
    public function recoverPage(string $nodeId, array|Data $fields, Options|array $options = []): ?Recovered
    {
        return $this->fetch(new RecoverDeletedPageRequest($nodeId, $fields, $this->options($options)));
    }
}
