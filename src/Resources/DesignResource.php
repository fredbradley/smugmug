<?php

namespace FredBradley\SmugMug\Resources;

use FredBradley\SmugMug\Data\Data;
use FredBradley\SmugMug\Data\Design\PageDesign;
use FredBradley\SmugMug\Data\Design\ProfileWidget;
use FredBradley\SmugMug\Data\Design\SiteDesign;
use FredBradley\SmugMug\Data\Design\UserSkin;
use FredBradley\SmugMug\Data\Design\Widget;
use FredBradley\SmugMug\Data\Image\Image;
use FredBradley\SmugMug\Listing;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\Design\ActivateSiteDesignRequest;
use FredBradley\SmugMug\Requests\Design\CloneSiteDesignRequest;
use FredBradley\SmugMug\Requests\Design\GetPageDesignRequest;
use FredBradley\SmugMug\Requests\Design\GetProfileWidgetRequest;
use FredBradley\SmugMug\Requests\Design\GetPublishedPageDesignRequest;
use FredBradley\SmugMug\Requests\Design\GetPublishedPageDesignWidgetsRequest;
use FredBradley\SmugMug\Requests\Design\GetPublishedProfileWidgetRequest;
use FredBradley\SmugMug\Requests\Design\GetPublishedUserSkinRequest;
use FredBradley\SmugMug\Requests\Design\GetSiteDesignFaviconImageRequest;
use FredBradley\SmugMug\Requests\Design\GetSiteDesignFolderDesignRequest;
use FredBradley\SmugMug\Requests\Design\GetSiteDesignGalleryDesignRequest;
use FredBradley\SmugMug\Requests\Design\GetSiteDesignGlobalDesignRequest;
use FredBradley\SmugMug\Requests\Design\GetSiteDesignHighlightImageRequest;
use FredBradley\SmugMug\Requests\Design\GetSiteDesignHomepageDesignRequest;
use FredBradley\SmugMug\Requests\Design\GetSiteDesignRequest;
use FredBradley\SmugMug\Requests\Design\GetUserAvailableSiteDesignsRequest;
use FredBradley\SmugMug\Requests\Design\GetUserRootSiteDesignRequest;
use FredBradley\SmugMug\Requests\Design\GetUserSiteDesignsRequest;
use FredBradley\SmugMug\Requests\Design\GetWorkingPageDesignRequest;
use FredBradley\SmugMug\Requests\Design\GetWorkingPageDesignWidgetsRequest;
use FredBradley\SmugMug\Requests\Design\GetWorkingProfileWidgetRequest;
use FredBradley\SmugMug\Requests\Design\GetWorkingUserSkinRequest;
use FredBradley\SmugMug\Requests\Design\PublishSiteDesignRequest;
use FredBradley\SmugMug\Requests\Design\UpdateSiteDesignRequest;
use FredBradley\SmugMug\Requests\Design\UpdateWorkingPageDesignRequest;
use FredBradley\SmugMug\Requests\Design\UpdateWorkingProfileWidgetRequest;

/**
 * Site designs, page designs, widgets and skins, from SmugMug::designs().
 */
class DesignResource extends BaseResource
{
    /**
     * A user's site designs: GET /api/v2/user/{nickname}!sitedesigns.
     *
     * @param  Options|array<string, mixed>  $options
     * @return Listing<SiteDesign>
     */
    public function forUser(string $nickname, Options|array $options = []): Listing
    {
        return $this->listing(fn (Options $o) => new GetUserSiteDesignsRequest($nickname, $o), $options);
    }

    /**
     * The site designs a user can start from: GET /api/v2/user/{nickname}!availabledesigns.
     *
     * @param  Options|array<string, mixed>  $options
     * @return Listing<SiteDesign>
     */
    public function available(string $nickname, Options|array $options = []): Listing
    {
        return $this->listing(fn (Options $o) => new GetUserAvailableSiteDesignsRequest($nickname, $o), $options);
    }

    /**
     * A user's active site design: GET /api/v2/user/{nickname}!rootsitedesign.
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function root(string $nickname, Options|array $options = []): ?SiteDesign
    {
        return $this->fetch(new GetUserRootSiteDesignRequest($nickname, $this->options($options)));
    }

    /**
     * A site design, by key: GET /api/v2/sitedesign/{siteDesignKey}.
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function get(string $siteDesignKey, Options|array $options = []): ?SiteDesign
    {
        return $this->fetch(new GetSiteDesignRequest($siteDesignKey, $this->options($options)));
    }

    /**
     * Change where a site design appears in the design picker: PATCH /api/v2/sitedesign/{siteDesignKey}.
     *
     *  - DesignPickerOrder, DesignPickerGroupName (Showcase Gallery, Slideshow, Photo Grid, Photo Collage, Photo Column or Spotlight Photo)
     *
     * @param  array<string, mixed>|Data  $fields
     * @param  Options|array<string, mixed>  $options
     */
    public function update(string $siteDesignKey, array|Data $fields, Options|array $options = []): ?SiteDesign
    {
        return $this->fetch(new UpdateSiteDesignRequest($siteDesignKey, $fields, $this->options($options)));
    }

    /**
     * Make a site design the active one: POST /api/v2/sitedesign/{siteDesignKey}!activate.
     *
     * @param  array<string, mixed>  $parameters
     * @param  Options|array<string, mixed>  $options
     * @return array<string, mixed>
     */
    public function activate(string $siteDesignKey, array $parameters = [], Options|array $options = []): array
    {
        return $this->fetch(new ActivateSiteDesignRequest($siteDesignKey, $parameters, $this->options($options)));
    }

    /**
     * Copy a site design: POST /api/v2/sitedesign/{siteDesignKey}!clone.
     *
     * @param  array<string, mixed>  $parameters
     * @param  Options|array<string, mixed>  $options
     * @return array<string, mixed>
     */
    public function clone(string $siteDesignKey, array $parameters = [], Options|array $options = []): array
    {
        return $this->fetch(new CloneSiteDesignRequest($siteDesignKey, $parameters, $this->options($options)));
    }

    /**
     * Publish a site design's unpublished changes: POST /api/v2/sitedesign/{siteDesignKey}!publish.
     *
     * @param  array<string, mixed>  $parameters
     * @param  Options|array<string, mixed>  $options
     * @return array<string, mixed>
     */
    public function publish(string $siteDesignKey, array $parameters = [], Options|array $options = []): array
    {
        return $this->fetch(new PublishSiteDesignRequest($siteDesignKey, $parameters, $this->options($options)));
    }

    /**
     * A site design's favicon: GET /api/v2/sitedesign/{siteDesignKey}!faviconimage.
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function faviconImage(string $siteDesignKey, Options|array $options = []): ?Image
    {
        return $this->fetch(new GetSiteDesignFaviconImageRequest($siteDesignKey, $this->options($options)));
    }

    /**
     * A site design's highlight image: GET /api/v2/sitedesign/{siteDesignKey}!highlightimage.
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function highlightImage(string $siteDesignKey, Options|array $options = []): ?Image
    {
        return $this->fetch(new GetSiteDesignHighlightImageRequest($siteDesignKey, $this->options($options)));
    }

    /**
     * The design of a site design's folder pages: GET /api/v2/sitedesign/{siteDesignKey}!folderdesign.
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function folderDesign(string $siteDesignKey, Options|array $options = []): ?PageDesign
    {
        return $this->fetch(new GetSiteDesignFolderDesignRequest($siteDesignKey, $this->options($options)));
    }

    /**
     * The design of a site design's gallery pages: GET /api/v2/sitedesign/{siteDesignKey}!gallerydesign.
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function galleryDesign(string $siteDesignKey, Options|array $options = []): ?PageDesign
    {
        return $this->fetch(new GetSiteDesignGalleryDesignRequest($siteDesignKey, $this->options($options)));
    }

    /**
     * The design every page of a site design shares: GET /api/v2/sitedesign/{siteDesignKey}!globaldesign.
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function globalDesign(string $siteDesignKey, Options|array $options = []): ?PageDesign
    {
        return $this->fetch(new GetSiteDesignGlobalDesignRequest($siteDesignKey, $this->options($options)));
    }

    /**
     * The design of a site design's homepage: GET /api/v2/sitedesign/{siteDesignKey}!homepagedesign.
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function homepageDesign(string $siteDesignKey, Options|array $options = []): ?PageDesign
    {
        return $this->fetch(new GetSiteDesignHomepageDesignRequest($siteDesignKey, $this->options($options)));
    }

    /**
     * A page design, by id: GET /api/v2/pagedesign/{pageDesignId}.
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function pageDesign(string $pageDesignId, Options|array $options = []): ?PageDesign
    {
        return $this->fetch(new GetPageDesignRequest($pageDesignId, $this->options($options)));
    }

    /**
     * The published version of a page design: GET /api/v2/pagedesign/{pageDesignId}/published.
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function publishedPageDesign(string $pageDesignId, Options|array $options = []): ?PageDesign
    {
        return $this->fetch(new GetPublishedPageDesignRequest($pageDesignId, $this->options($options)));
    }

    /**
     * The widgets on the published version of a page design: GET /api/v2/pagedesign/{pageDesignId}/published!widgets.
     *
     * @param  Options|array<string, mixed>  $options
     * @return Listing<Widget>
     */
    public function publishedWidgets(string $pageDesignId, Options|array $options = []): Listing
    {
        return $this->listing(fn (Options $o) => new GetPublishedPageDesignWidgetsRequest($pageDesignId, $o), $options);
    }

    /**
     * The working (unpublished) version of a page design: GET /api/v2/pagedesign/{pageDesignId}/working.
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function workingPageDesign(string $pageDesignId, Options|array $options = []): ?PageDesign
    {
        return $this->fetch(new GetWorkingPageDesignRequest($pageDesignId, $this->options($options)));
    }

    /**
     * Change the working version of a page design: PATCH /api/v2/pagedesign/{pageDesignId}/working.
     *
     *  - BackgroundType: none, photo, slideshow or video
     *  - BackgroundImageUri, BackgroundAlbumUri
     *  - BackgroundSlideshowSource: album, keyword, recent or selectedImages
     *
     * @param  array<string, mixed>|Data  $fields
     * @param  Options|array<string, mixed>  $options
     */
    public function updateWorkingPageDesign(string $pageDesignId, array|Data $fields, Options|array $options = []): ?PageDesign
    {
        return $this->fetch(new UpdateWorkingPageDesignRequest($pageDesignId, $fields, $this->options($options)));
    }

    /**
     * The widgets on the working version of a page design: GET /api/v2/pagedesign/{pageDesignId}/working!widgets.
     *
     * @param  Options|array<string, mixed>  $options
     * @return Listing<Widget>
     */
    public function workingWidgets(string $pageDesignId, Options|array $options = []): Listing
    {
        return $this->listing(fn (Options $o) => new GetWorkingPageDesignWidgetsRequest($pageDesignId, $o), $options);
    }

    /**
     * A profile widget, by id: GET /api/v2/widget/profile/{widgetId}.
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function widget(string $widgetId, Options|array $options = []): ?Widget
    {
        return $this->fetch(new GetProfileWidgetRequest($widgetId, $this->options($options)));
    }

    /**
     * The published settings of a profile widget: GET /api/v2/widget/profile/{widgetId}/published.
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function publishedWidget(string $widgetId, Options|array $options = []): ?ProfileWidget
    {
        return $this->fetch(new GetPublishedProfileWidgetRequest($widgetId, $this->options($options)));
    }

    /**
     * The working settings of a profile widget: GET /api/v2/widget/profile/{widgetId}/working.
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function workingWidget(string $widgetId, Options|array $options = []): ?ProfileWidget
    {
        return $this->fetch(new GetWorkingProfileWidgetRequest($widgetId, $this->options($options)));
    }

    /**
     * Change the working settings of a profile widget: PATCH /api/v2/widget/profile/{widgetId}/working.
     *
     *  - MarginTop, MarginRight, MarginBottom, MarginLeft
     *  - IconColor: default, primary, accent or official
     *  - IconType: default, square or circle
     *  - NewTab, ShowCoverPhoto, ShowEmail, ShowIcons, ShowProfilePhoto
     *
     * @param  array<string, mixed>|Data  $fields
     * @param  Options|array<string, mixed>  $options
     */
    public function updateWorkingWidget(string $widgetId, array|Data $fields, Options|array $options = []): ?ProfileWidget
    {
        return $this->fetch(new UpdateWorkingProfileWidgetRequest($widgetId, $fields, $this->options($options)));
    }

    /**
     * The published version of a site skin: GET /api/v2/userskin/{userSkinId}/published.
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function publishedSkin(string $userSkinId, Options|array $options = []): ?UserSkin
    {
        return $this->fetch(new GetPublishedUserSkinRequest($userSkinId, $this->options($options)));
    }

    /**
     * The working version of a site skin: GET /api/v2/userskin/{userSkinId}/working.
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function workingSkin(string $userSkinId, Options|array $options = []): ?UserSkin
    {
        return $this->fetch(new GetWorkingUserSkinRequest($userSkinId, $this->options($options)));
    }
}
