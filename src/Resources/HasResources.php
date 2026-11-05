<?php

namespace FredBradley\SmugMug\Resources;

/**
 * A resource for every part of SmugMug's API, on the connector and so on the SmugMug facade.
 */
trait HasResources
{
    /**
     * Users: accounts, profiles, the authenticated user, and everything that hangs off a user.
     */
    public function users(): UserResource
    {
        return new UserResource($this);
    }

    /**
     * Finding a folder, album, page or image by its address.
     */
    public function lookup(): LookupResource
    {
        return new LookupResource($this);
    }

    /**
     * Albums: their settings, images, comments, downloads and bulk image operations.
     */
    public function albums(): AlbumResource
    {
        return new AlbumResource($this);
    }

    /**
     * Images as they are in one album.
     */
    public function albumImages(): AlbumImageResource
    {
        return new AlbumImageResource($this);
    }

    /**
     * Images: their details, sizes, metadata and edits, one at a time or in bulk.
     */
    public function images(): ImageResource
    {
        return new ImageResource($this);
    }

    /**
     * Nodes: the folders, albums and pages of a site, by node id.
     */
    public function nodes(): NodeResource
    {
        return new NodeResource($this);
    }

    /**
     * Folders by path (the folder API, which nodes replace).
     */
    public function folders(): FolderResource
    {
        return new FolderResource($this);
    }

    /**
     * Comments.
     */
    public function comments(): CommentResource
    {
        return new CommentResource($this);
    }

    /**
     * Watermarks.
     */
    public function watermarks(): WatermarkResource
    {
        return new WatermarkResource($this);
    }

    /**
     * Printmarks.
     */
    public function printmarks(): PrintmarkResource
    {
        return new PrintmarkResource($this);
    }

    /**
     * Album templates.
     */
    public function albumTemplates(): AlbumTemplateResource
    {
        return new AlbumTemplateResource($this);
    }

    /**
     * Album display templates.
     */
    public function templates(): TemplateResource
    {
        return new TemplateResource($this);
    }

    /**
     * Album themes (legacy SmugMug).
     */
    public function themes(): ThemeResource
    {
        return new ThemeResource($this);
    }

    /**
     * Pages.
     */
    public function pages(): PageResource
    {
        return new PageResource($this);
    }

    /**
     * Prepared album downloads.
     */
    public function downloads(): DownloadResource
    {
        return new DownloadResource($this);
    }

    /**
     * SmugMug's in-app guides.
     */
    public function guides(): GuideResource
    {
        return new GuideResource($this);
    }

    /**
     * The progress of asynchronous image edits.
     */
    public function status(): StatusResource
    {
        return new StatusResource($this);
    }

    /**
     * Contacts, and the access granted to them.
     */
    public function contacts(): ContactResource
    {
        return new ContactResource($this);
    }

    /**
     * Deleted albums, folders and pages, and recovering them.
     */
    public function trash(): TrashResource
    {
        return new TrashResource($this);
    }

    /**
     * SmugMug's catalogue of products: vendors, categories, products and SKUs.
     */
    public function catalog(): CatalogResource
    {
        return new CatalogResource($this);
    }

    /**
     * Pricelists.
     */
    public function pricelists(): PricelistResource
    {
        return new PricelistResource($this);
    }

    /**
     * Coupons.
     */
    public function coupons(): CouponResource
    {
        return new CouponResource($this);
    }

    /**
     * Orders the user placed as a buyer.
     */
    public function orders(): OrderResource
    {
        return new OrderResource($this);
    }

    /**
     * Orders buyers placed with the user, and the payments for them.
     */
    public function sales(): SalesResource
    {
        return new SalesResource($this);
    }

    /**
     * Site designs, page designs, widgets and skins.
     */
    public function designs(): DesignResource
    {
        return new DesignResource($this);
    }

    /**
     * API keys, plans, subscriptions, invoices and the library.
     */
    public function account(): AccountResource
    {
        return new AccountResource($this);
    }

    /**
     * Uploads to SmugMug's uploader: new images and videos, and replacements.
     */
    public function uploads(): UploadResource
    {
        return new UploadResource($this);
    }
}
