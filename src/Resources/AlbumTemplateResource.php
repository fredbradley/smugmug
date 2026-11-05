<?php

namespace FredBradley\SmugMug\Resources;

use FredBradley\SmugMug\Data\Album\AlbumTemplate;
use FredBradley\SmugMug\Data\Data;
use FredBradley\SmugMug\Listing;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\AlbumTemplate\CreateAlbumTemplateRequest;
use FredBradley\SmugMug\Requests\AlbumTemplate\DeleteAlbumTemplateRequest;
use FredBradley\SmugMug\Requests\AlbumTemplate\GetAlbumTemplateRequest;
use FredBradley\SmugMug\Requests\AlbumTemplate\GetUserAlbumTemplatesRequest;
use FredBradley\SmugMug\Requests\AlbumTemplate\UpdateAlbumTemplateRequest;

/**
 * Album templates, from SmugMug::albumTemplates().
 */
class AlbumTemplateResource extends BaseResource
{
    /**
     * A user's album templates: GET /api/v2/user/{nickname}!albumtemplates.
     *
     *  - SortBy: Date Created
     *  - SortOrder: Ascending or Descending
     *
     * @param  Options|array<string, mixed>  $options
     * @return Listing<AlbumTemplate>
     */
    public function forUser(string $nickname, Options|array $options = []): Listing
    {
        return $this->listing(fn (Options $o) => new GetUserAlbumTemplatesRequest($nickname, $o), $options);
    }

    /**
     * Create an album template: POST /api/v2/user/{nickname}!albumtemplates.
     *
     *  - Name (required)
     *  - and any album setting (Privacy, Watermark, AllowDownloads, SortMethod and so on)
     *  - IsDefault: use it for new albums
     *
     * @param  array<string, mixed>|Data  $fields
     * @param  Options|array<string, mixed>  $options
     */
    public function create(string $nickname, array|Data $fields, Options|array $options = []): ?AlbumTemplate
    {
        return $this->fetch(new CreateAlbumTemplateRequest($nickname, $fields, $this->options($options)));
    }

    /**
     * An album template, by id: GET /api/v2/albumtemplate/{albumTemplateId}.
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function get(string $albumTemplateId, Options|array $options = []): ?AlbumTemplate
    {
        return $this->fetch(new GetAlbumTemplateRequest($albumTemplateId, $this->options($options)));
    }

    /**
     * Change an album template: PATCH /api/v2/albumtemplate/{albumTemplateId}.
     *
     *  - Name, IsDefault, and any album setting
     *
     * @param  array<string, mixed>|Data  $fields
     * @param  Options|array<string, mixed>  $options
     */
    public function update(string $albumTemplateId, array|Data $fields, Options|array $options = []): ?AlbumTemplate
    {
        return $this->fetch(new UpdateAlbumTemplateRequest($albumTemplateId, $fields, $this->options($options)));
    }

    /**
     * Delete an album template: DELETE /api/v2/albumtemplate/{albumTemplateId}.
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function delete(string $albumTemplateId, Options|array $options = []): bool
    {
        return $this->perform(new DeleteAlbumTemplateRequest($albumTemplateId, $this->options($options)));
    }
}
