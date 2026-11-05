<?php

namespace FredBradley\SmugMug\Resources;

use FredBradley\SmugMug\Data\Album\Album;
use FredBradley\SmugMug\Data\Data;
use FredBradley\SmugMug\Data\Image\Image;
use FredBradley\SmugMug\Data\Node\Node;
use FredBradley\SmugMug\Data\User\AddOnStatus;
use FredBradley\SmugMug\Data\User\CustomDomain;
use FredBradley\SmugMug\Data\User\Features;
use FredBradley\SmugMug\Data\User\Flags;
use FredBradley\SmugMug\Data\User\Grant;
use FredBradley\SmugMug\Data\User\GuideState;
use FredBradley\SmugMug\Data\User\HideGuides;
use FredBradley\SmugMug\Data\User\PhotoInvite;
use FredBradley\SmugMug\Data\User\ProductNotification;
use FredBradley\SmugMug\Data\User\SiteSettings;
use FredBradley\SmugMug\Data\User\Task;
use FredBradley\SmugMug\Data\User\TopKeywords;
use FredBradley\SmugMug\Data\User\UploadLimit;
use FredBradley\SmugMug\Data\User\User;
use FredBradley\SmugMug\Data\User\UserProfile;
use FredBradley\SmugMug\Listing;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\User\AcceptUserApiTermsRequest;
use FredBradley\SmugMug\Requests\User\AddUserFeaturedAlbumRequest;
use FredBradley\SmugMug\Requests\User\CreateUserRequest;
use FredBradley\SmugMug\Requests\User\DeleteUserRequest;
use FredBradley\SmugMug\Requests\User\GetAuthUserRequest;
use FredBradley\SmugMug\Requests\User\GetSiteUserRequest;
use FredBradley\SmugMug\Requests\User\GetUserAlbumsRequest;
use FredBradley\SmugMug\Requests\User\GetUserAssetsAlbumRequest;
use FredBradley\SmugMug\Requests\User\GetUserBioImageRequest;
use FredBradley\SmugMug\Requests\User\GetUserCollaboratorAddOnStatusRequest;
use FredBradley\SmugMug\Requests\User\GetUserCoverImageRequest;
use FredBradley\SmugMug\Requests\User\GetUserCustomDomainRequest;
use FredBradley\SmugMug\Requests\User\GetUserFeaturedAlbumsRequest;
use FredBradley\SmugMug\Requests\User\GetUserFeaturesRequest;
use FredBradley\SmugMug\Requests\User\GetUserFlagsRequest;
use FredBradley\SmugMug\Requests\User\GetUserGeoMediaRequest;
use FredBradley\SmugMug\Requests\User\GetUserGrantsRequest;
use FredBradley\SmugMug\Requests\User\GetUserGuideStatesRequest;
use FredBradley\SmugMug\Requests\User\GetUserHideGuidesRequest;
use FredBradley\SmugMug\Requests\User\GetUserPhotoInvitesRequest;
use FredBradley\SmugMug\Requests\User\GetUserPopularMediaRequest;
use FredBradley\SmugMug\Requests\User\GetUserProductNotificationsRequest;
use FredBradley\SmugMug\Requests\User\GetUserProfileRequest;
use FredBradley\SmugMug\Requests\User\GetUserRawManagementAddOnStatusRequest;
use FredBradley\SmugMug\Requests\User\GetUserRecentImagesRequest;
use FredBradley\SmugMug\Requests\User\GetUserRequest;
use FredBradley\SmugMug\Requests\User\GetUserRootNodeRequest;
use FredBradley\SmugMug\Requests\User\GetUserSiteSettingsRequest;
use FredBradley\SmugMug\Requests\User\GetUsersRequest;
use FredBradley\SmugMug\Requests\User\GetUserTasksRequest;
use FredBradley\SmugMug\Requests\User\GetUserTopKeywordsRequest;
use FredBradley\SmugMug\Requests\User\GetUserUploadLimitsRequest;
use FredBradley\SmugMug\Requests\User\LookupUserUrlPathRequest;
use FredBradley\SmugMug\Requests\User\RemoveUserFeaturedAlbumRequest;
use FredBradley\SmugMug\Requests\User\SearchUserDuplicateImagesRequest;
use FredBradley\SmugMug\Requests\User\SearchUserImagesRequest;
use FredBradley\SmugMug\Requests\User\SearchUsersRequest;
use FredBradley\SmugMug\Requests\User\SetUserHideGuidesRequest;
use FredBradley\SmugMug\Requests\User\SortUserFeaturedAlbumsRequest;
use FredBradley\SmugMug\Requests\User\UpdateAuthUserRequest;
use FredBradley\SmugMug\Requests\User\UpdateUserProfileRequest;
use FredBradley\SmugMug\Requests\User\UpdateUserRequest;

/**
 * Users: accounts, profiles, the authenticated user, and everything that hangs off a user, from SmugMug::users().
 */
class UserResource extends BaseResource
{
    /**
     * A user, by nickname: GET /api/v2/user/{nickname}.
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function get(string $nickname, Options|array $options = []): ?User
    {
        return $this->fetch(new GetUserRequest($nickname, $this->options($options)));
    }

    /**
     * As get(), but null when SmugMug has no such object or the credentials may not see it.
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function find(string $nickname, Options|array $options = []): ?User
    {
        return $this->fetchOrNull(new GetUserRequest($nickname, $this->options($options)));
    }

    /**
     * Several users at once (SmugMug's multi-get), by nickname: GET /api/v2/user/{nicknames}.
     *
     * @param  list<string>|string  $nicknames
     * @param  Options|array<string, mixed>  $options
     * @return Listing<User>
     */
    public function many(string|array $nicknames, Options|array $options = []): Listing
    {
        return $this->listing(fn (Options $o) => new GetUsersRequest($nicknames, $o), $options);
    }

    /**
     * Change the authenticated user's account settings: PATCH /api/v2/user/{nickname}.
     *
     *  - TimeZone: the user's time zone, such as Europe/London
     *  - Email: the account's email address
     *  - MfaEnabled: whether two-factor authentication is on
     *  - Password: the current password, needed to change the email or password
     *  - NewPassword: the new password
     *
     * @param  array<string, mixed>|Data  $fields
     * @param  Options|array<string, mixed>  $options
     */
    public function update(string $nickname, array|Data $fields, Options|array $options = []): ?User
    {
        return $this->fetch(new UpdateUserRequest($nickname, $fields, $this->options($options)));
    }

    /**
     * Close the authenticated user's account: DELETE /api/v2/user/{nickname}.
     *
     *  - Reason: why the account is being closed (query)
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function delete(string $nickname, Options|array $options = []): bool
    {
        return $this->perform(new DeleteUserRequest($nickname, $this->options($options)));
    }

    /**
     * Create a SmugMug account (for partner applications SmugMug has allowed to): POST /api/v2/user.
     *
     *  - Email (required): the new account's email address
     *  - FirstName (required)
     *  - LastName (required)
     *  - NickName: the nickname (and so the site address) to use
     *  - Password (required)
     *  - PlanUri (required): the URI of the plan to sign up for
     *  - IntentToSell (required): yes, no, maybe or skipped
     *  - TimeZone
     *  - Ureferrer: a referral code
     *
     * @param  array<string, mixed>|Data  $fields
     * @param  Options|array<string, mixed>  $options
     */
    public function create(array|Data $fields, Options|array $options = []): ?User
    {
        return $this->fetch(new CreateUserRequest($fields, $this->options($options)));
    }

    /**
     * Search SmugMug's users: GET /api/v2/user!search.
     *
     *  - q (required): the text to search for
     *
     * @param  Options|array<string, mixed>  $options
     * @return Listing<User>
     */
    public function search(Options|array $options = []): Listing
    {
        return $this->listing(fn (Options $o) => new SearchUsersRequest($o), $options);
    }

    /**
     * The user the access token belongs to: GET /api/v2!authuser.
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function me(Options|array $options = []): ?User
    {
        return $this->fetch(new GetAuthUserRequest($this->options($options)));
    }

    /**
     * Change the authenticated user's nickname: PATCH /api/v2!authuser.
     *
     *  - NickName: a new nickname
     *
     * @param  array<string, mixed>|Data  $fields
     * @param  Options|array<string, mixed>  $options
     */
    public function updateMe(array|Data $fields, Options|array $options = []): ?User
    {
        return $this->fetch(new UpdateAuthUserRequest($fields, $this->options($options)));
    }

    /**
     * The user whose site the request is made on (on a custom domain): GET /api/v2!siteuser.
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function siteUser(Options|array $options = []): ?User
    {
        return $this->fetch(new GetSiteUserRequest($this->options($options)));
    }

    /**
     * A user's profile: GET /api/v2/user/{nickname}!profile.
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function profile(string $nickname, Options|array $options = []): ?UserProfile
    {
        return $this->fetch(new GetUserProfileRequest($nickname, $this->options($options)));
    }

    /**
     * Change a user's profile: PATCH /api/v2/user/{nickname}!profile.
     *
     *  - BioText: the biography
     *  - BioImageUri: the URI of the image to use as the bio image
     *  - CoverImageUri: the URI of the image to use as the cover image
     *  - FirstName
     *  - LastName
     *  - DisplayName
     *  - Facebook, Twitter, Instagram, Blogger, FiveHundredPX, Flickr, LinkedIn, Pinterest, Tumblr, Vimeo, Wordpress, YouTube, Custom: links to the user's other sites
     *
     * @param  array<string, mixed>|Data  $fields
     * @param  Options|array<string, mixed>  $options
     */
    public function updateProfile(string $nickname, array|Data $fields, Options|array $options = []): ?UserProfile
    {
        return $this->fetch(new UpdateUserProfileRequest($nickname, $fields, $this->options($options)));
    }

    /**
     * The image on a user's bio: GET /api/v2/user/{nickname}!bioimage.
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function bioImage(string $nickname, Options|array $options = []): ?Image
    {
        return $this->fetch(new GetUserBioImageRequest($nickname, $this->options($options)));
    }

    /**
     * The cover image of a user's site: GET /api/v2/user/{nickname}!coverimage.
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function coverImage(string $nickname, Options|array $options = []): ?Image
    {
        return $this->fetch(new GetUserCoverImageRequest($nickname, $this->options($options)));
    }

    /**
     * What a user's plan lets them do: GET /api/v2/user/{nickname}!features.
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function features(string $nickname, Options|array $options = []): ?Features
    {
        return $this->fetch(new GetUserFeaturesRequest($nickname, $this->options($options)));
    }

    /**
     * The feature flags set for a user: GET /api/v2/user/{nickname}!flags.
     *
     *  - prefix: only the flags whose key starts with this
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function flags(string $nickname, Options|array $options = []): ?Flags
    {
        return $this->fetch(new GetUserFlagsRequest($nickname, $this->options($options)));
    }

    /**
     * A user's site settings: GET /api/v2/user/{nickname}!sitesettings.
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function siteSettings(string $nickname, Options|array $options = []): ?SiteSettings
    {
        return $this->fetch(new GetUserSiteSettingsRequest($nickname, $this->options($options)));
    }

    /**
     * The root node of a user's site: the top of their folders, albums and pages: GET /api/v2/user/{nickname}!rootnode.
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function rootNode(string $nickname, Options|array $options = []): ?Node
    {
        return $this->fetch(new GetUserRootNodeRequest($nickname, $this->options($options)));
    }

    /**
     * Every album a user has (that the credentials may see): GET /api/v2/user/{nickname}!albums.
     *
     *  - Order: Last Updated (Descending), Last Updated (Ascending), Date Added (Descending) or Date Added (Ascending)
     *
     * @param  Options|array<string, mixed>  $options
     * @return Listing<Album>
     */
    public function albums(string $nickname, Options|array $options = []): Listing
    {
        return $this->listing(fn (Options $o) => new GetUserAlbumsRequest($nickname, $o), $options);
    }

    /**
     * The albums a user features on their site: GET /api/v2/user/{nickname}!featuredalbums.
     *
     * @param  Options|array<string, mixed>  $options
     * @return Listing<Album>
     */
    public function featuredAlbums(string $nickname, Options|array $options = []): Listing
    {
        return $this->listing(fn (Options $o) => new GetUserFeaturedAlbumsRequest($nickname, $o), $options);
    }

    /**
     * Feature an album: POST /api/v2/user/{nickname}!featuredalbums.
     *
     *  - AlbumUri (required): the URI of the album to feature
     *
     * @param  array<string, mixed>  $parameters
     * @param  Options|array<string, mixed>  $options
     * @return array<string, mixed>
     */
    public function addFeaturedAlbum(string $nickname, array $parameters = [], Options|array $options = []): array
    {
        return $this->fetch(new AddUserFeaturedAlbumRequest($nickname, $parameters, $this->options($options)));
    }

    /**
     * Stop featuring an album: DELETE /api/v2/user/{nickname}!featuredalbums.
     *
     *  - UserAlbumUri (required): the URI of the featured album to stop featuring
     *
     * @param  array<string, mixed>  $parameters
     * @param  Options|array<string, mixed>  $options
     * @return array<string, mixed>
     */
    public function removeFeaturedAlbum(string $nickname, array $parameters = [], Options|array $options = []): array
    {
        return $this->fetch(new RemoveUserFeaturedAlbumRequest($nickname, $parameters, $this->options($options)));
    }

    /**
     * Reorder a user's featured albums: POST /api/v2/user/{nickname}!sortfeaturedalbums.
     *
     *  - MoveUris (required): the featured albums to move (comma-separated URIs)
     *  - MoveLocation (required): Before or After
     *  - Uri (required): the featured album to move them before or after
     *
     * @param  array<string, mixed>  $parameters
     * @param  Options|array<string, mixed>  $options
     * @return array<string, mixed>
     */
    public function sortFeaturedAlbums(string $nickname, array $parameters = [], Options|array $options = []): array
    {
        return $this->fetch(new SortUserFeaturedAlbumsRequest($nickname, $parameters, $this->options($options)));
    }

    /**
     * A user's images that carry a location: GET /api/v2/user/{nickname}!geomedia.
     *
     *  - SortMethod: Popular, Oldest or Newest
     *  - MediaType: All, Images or Videos
     *
     * @param  Options|array<string, mixed>  $options
     * @return Listing<Image>
     */
    public function geoMedia(string $nickname, Options|array $options = []): Listing
    {
        return $this->listing(fn (Options $o) => new GetUserGeoMediaRequest($nickname, $o), $options);
    }

    /**
     * A user's most viewed images: GET /api/v2/user/{nickname}!popularmedia.
     *
     *  - MediaType: All, Images or Videos
     *
     * @param  Options|array<string, mixed>  $options
     * @return Listing<Image>
     */
    public function popularMedia(string $nickname, Options|array $options = []): Listing
    {
        return $this->listing(fn (Options $o) => new GetUserPopularMediaRequest($nickname, $o), $options);
    }

    /**
     * A user's most recently uploaded images: GET /api/v2/user/{nickname}!recentimages.
     *
     *  - MediaType: All, Images or Videos
     *
     * @param  Options|array<string, mixed>  $options
     * @return Listing<Image>
     */
    public function recentImages(string $nickname, Options|array $options = []): Listing
    {
        return $this->listing(fn (Options $o) => new GetUserRecentImagesRequest($nickname, $o), $options);
    }

    /**
     * Search a user's images: GET /api/v2/user/{nickname}!imagesearch.
     *
     *  - q (required): the text to search for
     *  - Order: Popular, Oldest or Newest
     *
     * @param  Options|array<string, mixed>  $options
     * @return Listing<Image>
     */
    public function searchImages(string $nickname, Options|array $options = []): Listing
    {
        return $this->listing(fn (Options $o) => new SearchUserImagesRequest($nickname, $o), $options);
    }

    /**
     * Images in an album with the same file name, to avoid uploading one twice: GET /api/v2/user/{nickname}!duplicateimagesearch.
     *
     *  - AlbumUri (required): the album to look in
     *  - FileName (required): the file name to look for
     *
     * @param  Options|array<string, mixed>  $options
     * @return Listing<Image>
     */
    public function duplicateImages(string $nickname, Options|array $options = []): Listing
    {
        return $this->listing(fn (Options $o) => new SearchUserDuplicateImagesRequest($nickname, $o), $options);
    }

    /**
     * The keywords a user uses most: GET /api/v2/user/{nickname}!topkeywords.
     *
     *  - NumKeywords: how many keywords
     *  - NodeURI: only the keywords under this node
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function topKeywords(string $nickname, Options|array $options = []): ?TopKeywords
    {
        return $this->fetch(new GetUserTopKeywordsRequest($nickname, $this->options($options)));
    }

    /**
     * The folder, album or page at a path of a user's site: GET /api/v2/user/{nickname}!urlpathlookup.
     *
     *  - urlpath (required): the path below the user's site, such as /Travel/2024
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function urlPathLookup(string $nickname, Options|array $options = []): mixed
    {
        return $this->fetch(new LookupUserUrlPathRequest($nickname, $this->options($options)));
    }

    /**
     * The hidden album that holds one kind of the user's site assets: GET /api/v2/user/{nickname}!assetsalbum.
     *
     *  - Type (required): Logo Images, Profile Images, Cover Images, Printmark Images or Watermark Images
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function assetsAlbum(string $nickname, Options|array $options = []): ?Album
    {
        return $this->fetch(new GetUserAssetsAlbumRequest($nickname, $this->options($options)));
    }

    /**
     * What the user may upload: accepted file types and largest sizes: GET /api/v2/user/{nickname}!uploadlimits.
     *
     * @param  Options|array<string, mixed>  $options
     * @return Listing<UploadLimit>
     */
    public function uploadLimits(string $nickname, Options|array $options = []): Listing
    {
        return $this->listing(fn (Options $o) => new GetUserUploadLimitsRequest($nickname, $o), $options);
    }

    /**
     * The custom domain of a user's site: GET /api/v2/user/{nickname}!customdomain.
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function customDomain(string $nickname, Options|array $options = []): ?CustomDomain
    {
        return $this->fetch(new GetUserCustomDomainRequest($nickname, $this->options($options)));
    }

    /**
     * The background tasks SmugMug is running for a user: GET /api/v2/user/{nickname}!tasks.
     *
     *  - status: Any, Pending, Done or Error
     *
     * @param  Options|array<string, mixed>  $options
     * @return Listing<Task>
     */
    public function tasks(string $nickname, Options|array $options = []): Listing
    {
        return $this->listing(fn (Options $o) => new GetUserTasksRequest($nickname, $o), $options);
    }

    /**
     * A user's photo invitations: GET /api/v2/user/{nickname}!photoinvites.
     *
     * @param  Options|array<string, mixed>  $options
     * @return Listing<PhotoInvite>
     */
    public function photoInvites(string $nickname, Options|array $options = []): Listing
    {
        return $this->listing(fn (Options $o) => new GetUserPhotoInvitesRequest($nickname, $o), $options);
    }

    /**
     * Which of SmugMug's guides a user has completed: GET /api/v2/user/{nickname}!guides.
     *
     *  - OnlyAutorun: only the guides that start by themselves
     *  - IncludeInvisible: include hidden guides
     *
     * @param  Options|array<string, mixed>  $options
     * @return Listing<GuideState>
     */
    public function guides(string $nickname, Options|array $options = []): Listing
    {
        return $this->listing(fn (Options $o) => new GetUserGuideStatesRequest($nickname, $o), $options);
    }

    /**
     * Whether SmugMug's guides are hidden for a user: GET /api/v2/user/{nickname}!hideguides.
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function hideGuides(string $nickname, Options|array $options = []): ?HideGuides
    {
        return $this->fetch(new GetUserHideGuidesRequest($nickname, $this->options($options)));
    }

    /**
     * Hide or show SmugMug's guides for a user: POST /api/v2/user/{nickname}!hideguides.
     *
     *  - Hide (required): true to hide the guides
     *
     * @param  array<string, mixed>|Data  $fields
     * @param  Options|array<string, mixed>  $options
     */
    public function setHideGuides(string $nickname, array|Data $fields, Options|array $options = []): ?HideGuides
    {
        return $this->fetch(new SetUserHideGuidesRequest($nickname, $fields, $this->options($options)));
    }

    /**
     * The access a user has granted to their contacts: GET /api/v2/user/{nickname}!grants.
     *
     *  - OwnerUri: only the grants on this album, folder or node
     *
     * @param  Options|array<string, mixed>  $options
     * @return Listing<Grant>
     */
    public function grants(string $nickname, Options|array $options = []): Listing
    {
        return $this->listing(fn (Options $o) => new GetUserGrantsRequest($nickname, $o), $options);
    }

    /**
     * A user's product notifications: GET /api/v2/user/{nickname}!productnotifications.
     *
     * @param  Options|array<string, mixed>  $options
     * @return Listing<ProductNotification>
     */
    public function productNotifications(string $nickname, Options|array $options = []): Listing
    {
        return $this->listing(fn (Options $o) => new GetUserProductNotificationsRequest($nickname, $o), $options);
    }

    /**
     * The state of a user's RAW file management add-on: GET /api/v2/user/{nickname}!rawmanagementaddonstatus.
     *
     *  - Currency: the currency to show prices in
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function rawManagementAddOn(string $nickname, Options|array $options = []): ?AddOnStatus
    {
        return $this->fetch(new GetUserRawManagementAddOnStatusRequest($nickname, $this->options($options)));
    }

    /**
     * The state of a user's collaborator seats add-on: GET /api/v2/user/{nickname}!collaboratoraddonstatus.
     *
     *  - Currency: the currency to show prices in
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function collaboratorAddOn(string $nickname, Options|array $options = []): ?AddOnStatus
    {
        return $this->fetch(new GetUserCollaboratorAddOnStatusRequest($nickname, $this->options($options)));
    }

    /**
     * Accept SmugMug's API terms of service for a user: POST /api/v2/user/{nickname}!acceptapiterms.
     *
     *  - AcceptTermsOfService (required): true
     *
     * @param  array<string, mixed>  $parameters
     * @param  Options|array<string, mixed>  $options
     * @return array<string, mixed>
     */
    public function acceptApiTerms(string $nickname, array $parameters = [], Options|array $options = []): array
    {
        return $this->fetch(new AcceptUserApiTermsRequest($nickname, $parameters, $this->options($options)));
    }
}
