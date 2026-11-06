# Endpoints

Every call the package makes to SmugMug's API v2, by part of the API: 322 endpoints from SmugMug's own Live API
Browser, plus OAuth sign-in and uploads.

Resources hang off the facade (or any connector): `SmugMug::{resource}()->{call}()`. Requests are plain Saloon
classes under `FredBradley\SmugMug\Requests\{Area}`, for `SmugMug::send()`.

How to read the tables:

- **Arguments.** Keys go in the path: `$nickname`, `$albumKey`, `$imageKey` (with its serial, as in `jPPKD2c-1`),
  `$nodeId` and so on.
  - A folder's `$path` is its path below the user's site (`'Travel/2024'`), or `''` for the root.
  - A multi-get takes a list of keys.
  - Every call also takes `$options` last: an `Options` or an array of query parameters (`_expand`, `_filter`,
    `start`, `count`, and the endpoint's own parameters in the **Parameters** column).
- **Writes.** A create or update (POST or PATCH) takes `$fields`: SmugMug's field names and values, or a model. A
  PATCH changes only the fields it is given. An action takes `$parameters` and returns the envelope's `Response` as
  an array, which for an asynchronous action (`Async: true`) holds the URI of its status.
- **Required parameters.** A parameter marked *(required)* must be given. "A list of URIs" can be a PHP list; the
  package joins it with commas where SmugMug wants one string.
- **Returns.** A list returns a `Listing`: `get()` for one page, `lazy()` or `all()` for every item, `first()`,
  `total()`. An item returns its model, or null when SmugMug answers with no object. A call SmugMug answers with
  an error throws a `SmugMugApiException`, such as `NotFoundException` for a 404.
- **Permissions.** Owner-only calls need an access token (`SmugMug::withToken($token, $secret)`):
  - every PATCH and DELETE needs *Modify*
  - every create (POST) needs *Add*
  - private data needs *Access Full*
- **Not covered by a resource.** SmugMug also accepts PUT, with the same parameters, wherever it takes POST for
  `!applyalbumtemplate`, `!collectimages`, `!sortimages` and `!sortfeaturedalbums`. Send those, and the base
  endpoints that only list URIs (`/api/v2/album`, `/api/v2/image` and so on), with `SmugMug::put()` and
  `SmugMug::get()`.

## Signing in

`SmugMug::oauth()`: OAuth 1.0a, signed with HMAC-SHA1.

| Call | Does | Returns |
|---|---|---|
| `requestToken(?string $callback)` | Gets a request token, signed by the application with `oauth_callback`. The callback defaults to `smugmug.callback_url`, then `oob`. | `RequestToken` |
| `authorizationUrl($requestToken, Access $access = Access::Full, Permissions $permissions = Permissions::Read, array $options = [])` | Gives the address to send the user to. `$options` takes `allowThirdPartyLogin`, `showSignUpButton`, `username` and `viewportScale`. | string |
| `accessToken(RequestToken $requestToken, string $verifier)` | Trades the approved request token and its verifier for the user's access token. | `AccessToken` |
| `connectorFor(AccessToken $accessToken)` | Gives the connector acting as that user. | `SmugMugConnector` |

| Request | Method | Path |
|---|---|---|
| `OAuth\GetRequestTokenRequest` | GET | `/services/oauth/1.0a/getRequestToken` |
| `OAuth\GetAccessTokenRequest` | GET | `/services/oauth/1.0a/getAccessToken` |

## Uploads

`SmugMug::uploads()`: every upload needs an access token with *Add* permission, or *Modify* to replace a file. The
`$media` argument is a `Media` (`Media::fromPath()`, `Media::fromContents()`) or a file path.

| Call | Does | Returns | Parameters |
|---|---|---|---|
| `upload($album, $media, array $details = [])` | Uploads a file into an album with the uploader. `$album` is the album's key or URI. | `UploadResult` | Title, Caption, Keywords (a list or a semicolon-separated string), Latitude, Longitude, Altitude, Hidden, FileName, Pretty |
| `replace($album, $imageUri, $media, array $details = [])` | Replaces an image's file with the uploader, keeping its key, title, caption and comments. | `UploadResult` | as `upload()` |
| `toAlbum($albumKey, $media, array $fields = [])` | Adds a file to an album through the API, as multipart form data. | the `AlbumImage` | Title, Caption, Keywords (a list), Hidden, ReplaceMediaUri |
| `replaceMedia($imageKey, $media)` | Replaces an image's file through the API. | the `Response` array | |

| Request | Method | Path |
|---|---|---|
| `Upload\UploadImageRequest` | POST | `https://upload.smugmug.com/` (with X-Smug-AlbumUri, X-Smug-Version, X-Smug-ResponseType, Content-MD5) |
| `Upload\CreateAlbumImageRequest` | POST | `/api/v2/album/{albumKey}!images` |
| `Upload\ReplaceImageMediaRequest` | POST | `/api/v2/image/{imageKey}!replace` |

## Any URI

| Call | Does |
|---|---|
| `SmugMug::follow($uri, $options)` | Fetches a URI SmugMug gave you, such as `$album->uri('AlbumImages')` or a page's NextPage. Returns its model, a Collection of them, or arrays. |
| `SmugMug::get($uri, $options)`, `delete()` | Sends a GET or DELETE to any path on api.smugmug.com. Returns the Saloon `Response`. |
| `SmugMug::post($uri, $body, $options)`, `patch()`, `put()` | Sends JSON to any path on api.smugmug.com. Returns the Saloon `Response`. |

## Users

`SmugMug::users()`: users: accounts, profiles, the authenticated user, and everything that hangs off a user.

| Call | Does | Returns | Parameters |
|---|---|---|---|
| `get($nickname)` | A user, by nickname | `User` or null |  |
| `find($nickname)` | As `get()`, null when SmugMug has no such object | `User` or null | |
| `many(array $nicknames)` | Several users at once (SmugMug's multi-get), by nickname | a `Listing` of `User` |  |
| `update($nickname, $fields)` | Change the authenticated user's account settings | `User` or null | TimeZone: the user's time zone, such as Europe/London<br>Email: the account's email address<br>MfaEnabled: whether two-factor authentication is on<br>Password: the current password, needed to change the email or password<br>NewPassword: the new password |
| `delete($nickname)` | Close the authenticated user's account | true | Reason: why the account is being closed (query) |
| `create($fields)` | Create a SmugMug account (for partner applications SmugMug has allowed to) | `User` or null | Email (required): the new account's email address<br>FirstName (required)<br>LastName (required)<br>NickName: the nickname (and so the site address) to use<br>Password (required)<br>PlanUri (required): the URI of the plan to sign up for<br>IntentToSell (required): yes, no, maybe or skipped<br>TimeZone<br>Ureferrer: a referral code |
| `search()` | Search SmugMug's users | a `Listing` of `User` | q (required): the text to search for |
| `me()` | The user the access token belongs to. Needs an access token: without one it throws a `LogicException` | `User` or null |  |
| `updateMe($fields)` | Change the authenticated user's nickname. Needs an access token | `User` or null | NickName: a new nickname |
| `siteUser()` | The user whose site the request is made on (on a custom domain) | `User` or null |  |
| `profile($nickname)` | A user's profile | `UserProfile` or null |  |
| `updateProfile($nickname, $fields)` | Change a user's profile | `UserProfile` or null | BioText: the biography<br>BioImageUri: the URI of the image to use as the bio image<br>CoverImageUri: the URI of the image to use as the cover image<br>FirstName<br>LastName<br>DisplayName<br>Facebook, Twitter, Instagram, Blogger, FiveHundredPX, Flickr, LinkedIn, Pinterest, Tumblr, Vimeo, Wordpress, YouTube, Custom: links to the user's other sites |
| `bioImage($nickname)` | The image on a user's bio | `Image` or null |  |
| `coverImage($nickname)` | The cover image of a user's site | `Image` or null |  |
| `features($nickname)` | What a user's plan lets them do | `Features` or null |  |
| `flags($nickname)` | The feature flags set for a user | `Flags` or null | prefix: only the flags whose key starts with this |
| `siteSettings($nickname)` | A user's site settings | `SiteSettings` or null |  |
| `rootNode($nickname)` | The root node of a user's site: the top of their folders, albums and pages | `Node` or null |  |
| `albums($nickname)` | Every album a user has (that the credentials may see) | a `Listing` of `Album` | Order: Last Updated (Descending), Last Updated (Ascending), Date Added (Descending) or Date Added (Ascending) |
| `featuredAlbums($nickname)` | The albums a user features on their site | a `Listing` of `Album` |  |
| `addFeaturedAlbum($nickname, $parameters)` | Feature an album | the `Response` array | AlbumUri (required): the URI of the album to feature |
| `removeFeaturedAlbum($nickname, $parameters)` | Stop featuring an album | the `Response` array | UserAlbumUri (required): the URI of the featured album to stop featuring |
| `sortFeaturedAlbums($nickname, $parameters)` | Reorder a user's featured albums | the `Response` array | MoveUris (required): the featured albums to move (comma-separated URIs)<br>MoveLocation (required): Before or After<br>Uri (required): the featured album to move them before or after |
| `geoMedia($nickname)` | A user's images that carry a location | a `Listing` of `Image` | SortMethod: Popular, Oldest or Newest<br>MediaType: All, Images or Videos |
| `popularMedia($nickname)` | A user's most viewed images | a `Listing` of `Image` | MediaType: All, Images or Videos |
| `recentImages($nickname)` | A user's most recently uploaded images | a `Listing` of `Image` | MediaType: All, Images or Videos |
| `searchImages($nickname)` | Search a user's images | a `Listing` of `Image` | q (required): the text to search for<br>Order: Popular, Oldest or Newest |
| `duplicateImages($nickname)` | Images in an album with the same file name, to avoid uploading one twice | a `Listing` of `Image` | AlbumUri (required): the album to look in<br>FileName (required): the file name to look for |
| `topKeywords($nickname)` | The keywords a user uses most | `TopKeywords` or null | NumKeywords: how many keywords<br>NodeURI: only the keywords under this node |
| `urlPathLookup($nickname)` | The folder, album or page at a path of a user's site | the model for the Locator (arrays when there is none) or null | urlpath (required): the path below the user's site, such as /Travel/2024 |
| `assetsAlbum($nickname)` | The hidden album that holds one kind of the user's site assets | `Album` or null | Type (required): Logo Images, Profile Images, Cover Images, Printmark Images or Watermark Images |
| `uploadLimits($nickname)` | What the user may upload: accepted file types and largest sizes | a `Listing` of `UploadLimit` |  |
| `customDomain($nickname)` | The custom domain of a user's site | `CustomDomain` or null |  |
| `tasks($nickname)` | The background tasks SmugMug is running for a user | a `Listing` of `Task` | status: Any, Pending, Done or Error |
| `photoInvites($nickname)` | A user's photo invitations | a `Listing` of `PhotoInvite` |  |
| `guides($nickname)` | Which of SmugMug's guides a user has completed | a `Listing` of `GuideState` | OnlyAutorun: only the guides that start by themselves<br>IncludeInvisible: include hidden guides |
| `hideGuides($nickname)` | Whether SmugMug's guides are hidden for a user | `HideGuides` or null |  |
| `setHideGuides($nickname, $fields)` | Hide or show SmugMug's guides for a user | `HideGuides` or null | Hide (required): true to hide the guides |
| `grants($nickname)` | The access a user has granted to their contacts | a `Listing` of `Grant` | OwnerUri: only the grants on this album, folder or node |
| `productNotifications($nickname)` | A user's product notifications | a `Listing` of `ProductNotification` |  |
| `rawManagementAddOn($nickname)` | The state of a user's RAW file management add-on | `AddOnStatus` or null | Currency: the currency to show prices in |
| `collaboratorAddOn($nickname)` | The state of a user's collaborator seats add-on | `AddOnStatus` or null | Currency: the currency to show prices in |
| `acceptApiTerms($nickname, $parameters)` | Accept SmugMug's API terms of service for a user | the `Response` array | AcceptTermsOfService (required): true |

| Request | Method | Path |
|---|---|---|
| `User\GetUserRequest` | GET | `/api/v2/user/{nickname}` |
| `User\GetUsersRequest` | GET | `/api/v2/user/{nicknames}` |
| `User\UpdateUserRequest` | PATCH | `/api/v2/user/{nickname}` |
| `User\DeleteUserRequest` | DELETE | `/api/v2/user/{nickname}` |
| `User\CreateUserRequest` | POST | `/api/v2/user` |
| `User\SearchUsersRequest` | GET | `/api/v2/user!search` |
| `User\GetAuthUserRequest` | GET | `/api/v2!authuser` |
| `User\UpdateAuthUserRequest` | PATCH | `/api/v2!authuser` |
| `User\GetSiteUserRequest` | GET | `/api/v2!siteuser` |
| `User\GetUserProfileRequest` | GET | `/api/v2/user/{nickname}!profile` |
| `User\UpdateUserProfileRequest` | PATCH | `/api/v2/user/{nickname}!profile` |
| `User\GetUserBioImageRequest` | GET | `/api/v2/user/{nickname}!bioimage` |
| `User\GetUserCoverImageRequest` | GET | `/api/v2/user/{nickname}!coverimage` |
| `User\GetUserFeaturesRequest` | GET | `/api/v2/user/{nickname}!features` |
| `User\GetUserFlagsRequest` | GET | `/api/v2/user/{nickname}!flags` |
| `User\GetUserSiteSettingsRequest` | GET | `/api/v2/user/{nickname}!sitesettings` |
| `User\GetUserRootNodeRequest` | GET | `/api/v2/user/{nickname}!rootnode` |
| `User\GetUserAlbumsRequest` | GET | `/api/v2/user/{nickname}!albums` |
| `User\GetUserFeaturedAlbumsRequest` | GET | `/api/v2/user/{nickname}!featuredalbums` |
| `User\AddUserFeaturedAlbumRequest` | POST | `/api/v2/user/{nickname}!featuredalbums` |
| `User\RemoveUserFeaturedAlbumRequest` | DELETE | `/api/v2/user/{nickname}!featuredalbums` |
| `User\SortUserFeaturedAlbumsRequest` | POST | `/api/v2/user/{nickname}!sortfeaturedalbums` |
| `User\GetUserGeoMediaRequest` | GET | `/api/v2/user/{nickname}!geomedia` |
| `User\GetUserPopularMediaRequest` | GET | `/api/v2/user/{nickname}!popularmedia` |
| `User\GetUserRecentImagesRequest` | GET | `/api/v2/user/{nickname}!recentimages` |
| `User\SearchUserImagesRequest` | GET | `/api/v2/user/{nickname}!imagesearch` |
| `User\SearchUserDuplicateImagesRequest` | GET | `/api/v2/user/{nickname}!duplicateimagesearch` |
| `User\GetUserTopKeywordsRequest` | GET | `/api/v2/user/{nickname}!topkeywords` |
| `User\LookupUserUrlPathRequest` | GET | `/api/v2/user/{nickname}!urlpathlookup` |
| `User\GetUserAssetsAlbumRequest` | GET | `/api/v2/user/{nickname}!assetsalbum` |
| `User\GetUserUploadLimitsRequest` | GET | `/api/v2/user/{nickname}!uploadlimits` |
| `User\GetUserCustomDomainRequest` | GET | `/api/v2/user/{nickname}!customdomain` |
| `User\GetUserTasksRequest` | GET | `/api/v2/user/{nickname}!tasks` |
| `User\GetUserPhotoInvitesRequest` | GET | `/api/v2/user/{nickname}!photoinvites` |
| `User\GetUserGuideStatesRequest` | GET | `/api/v2/user/{nickname}!guides` |
| `User\GetUserHideGuidesRequest` | GET | `/api/v2/user/{nickname}!hideguides` |
| `User\SetUserHideGuidesRequest` | POST | `/api/v2/user/{nickname}!hideguides` |
| `User\GetUserGrantsRequest` | GET | `/api/v2/user/{nickname}!grants` |
| `User\GetUserProductNotificationsRequest` | GET | `/api/v2/user/{nickname}!productnotifications` |
| `User\GetUserRawManagementAddOnStatusRequest` | GET | `/api/v2/user/{nickname}!rawmanagementaddonstatus` |
| `User\GetUserCollaboratorAddOnStatusRequest` | GET | `/api/v2/user/{nickname}!collaboratoraddonstatus` |
| `User\AcceptUserApiTermsRequest` | POST | `/api/v2/user/{nickname}!acceptapiterms` |

## Lookups

`SmugMug::lookup()`: finding a folder, album, page or image by its address.

| Call | Does | Returns | Parameters |
|---|---|---|---|
| `urlPath()` | The folder, album or page at a user's path | the model for the Locator (arrays when there is none) or null | nickname (required): the user<br>urlpath (required): the path below their site |
| `webUri()` | The folder, album, page or album image a SmugMug web address shows | the model for the Locator (arrays when there is none) or null | WebUri (required): the address of a page of a SmugMug site |
| `oldNodeId()` | The node that a legacy (pre-node) id became | `Node` or null | ID (required): the numeric id of the legacy category or subcategory |

| Request | Method | Path |
|---|---|---|
| `Lookup\LookupNicknameUrlPathRequest` | GET | `/api/v2!nicknameurlpathlookup` |
| `Lookup\LookupWebUriRequest` | GET | `/api/v2!weburilookup` |
| `Lookup\LookupOldNodeIdRequest` | GET | `/api/v2/node!oldidlookup` |

## Albums

`SmugMug::albums()`: albums: their settings, images, comments, downloads and bulk image operations.

| Call | Does | Returns | Parameters |
|---|---|---|---|
| `get($albumKey)` | An album, by key | `Album` or null |  |
| `find($albumKey)` | As `get()`, null when SmugMug has no such object | `Album` or null | |
| `many(array $albumKeys)` | Several albums at once (multi-get), by key | a `Listing` of `Album` |  |
| `update($albumKey, $fields)` | Change an album's settings | `Album` or null | Name, UrlName, Description, Keywords: what the album is called and says<br>Privacy: Public, Unlisted or Private<br>SecurityType: None, Password or GrantAccess<br>Password, PasswordHint<br>SmugSearchable: No or Inherit from User<br>WorldSearchable<br>SortMethod: Position, Caption, Filename, Date Uploaded, Date Modified or Date Taken<br>SortDirection: Ascending or Descending<br>AllowDownloads, MaxPhotoDownloadSize, DownloadPassword: downloads<br>LargestSize: the largest size visitors may see (Medium to Original)<br>Watermark, WatermarkUri: watermarking<br>Printable, PrintmarkUri, Backprinting, BoutiquePackaging, InterceptShipping, PackagingBranding, ProofDays, ProofDigital, CommerceLightbox: selling<br>Comments, CanRank, Share, Slideshow, Clean, EXIF, Filenames, Geography, HideOwner, Protected, SquareThumbs, FamilyEdit, FriendEdit: display and interaction<br>Header: Custom or SmugMug<br>ThemeUri, TemplateUri, AlbumTemplateUri, HighlightAlbumImageUri<br>UploadKey: the guest upload key<br>AutoRename: rename instead of failing when the UrlName is taken |
| `delete($albumKey)` | Delete an album and its images (to the trash) | true |  |
| `search()` | Search albums | a `Listing` of `Album` | Scope (required): the URI of the user or node to search in<br>Text: the text to search for<br>SortMethod: Rank or LastUpdated<br>SortDirection: Ascending or Descending |
| `images($albumKey)` | The images in an album | a `Listing` of `AlbumImage` | FileNames: only these file names (comma-separated)<br>SkuId: only the images that may be sold as this SKU |
| `popularMedia($albumKey)` | An album's most viewed images | a `Listing` of `AlbumImage` | MediaType: All, Images or Videos |
| `geoMedia($albumKey)` | An album's images that carry a location | a `Listing` of `AlbumImage` | SortMethod: Popular, Oldest or Newest<br>MediaType: All, Images or Videos |
| `highlightImage($albumKey)` | The image that represents an album | `AlbumImage` or null |  |
| `shareUris($albumKey)` | The links to share an album | `ShareUris` or null |  |
| `comments($albumKey)` | The comments on an album | a `Listing` of `Comment` |  |
| `addComment($albumKey, $fields)` | Comment on an album | `Comment` or null | Comment (required): the text<br>Name, Email, Link: who is commenting, for a comment without a token<br>Rating: a rating<br>SocialID, ServiceID: a social sign-in |
| `downloads($albumKey)` | The downloads of an album's originals | a `Listing` of `Download` |  |
| `createDownload($albumKey, $parameters)` | Prepare a download (zip) of an album's originals | the `Response` array |  |
| `updateDownload($albumKey, $parameters)` | Prepare the album's download again | the `Response` array |  |
| `deleteDownload($albumKey)` | Delete an album's prepared downloads | true |  |
| `prices($albumKey)` | What an album's images sell for | a `Listing` of `CatalogSkuPrice` | Currency: USD, CAD, GBP, EUR, AUD, JPY, CHF, NZD, SEK or HKD |
| `pricelistExclusions($albumKey)` | The pricelist products that may not be bought from an album | `PricelistExclusions` or null |  |
| `grants($albumKey)` | Who has been granted access to an album | a `Listing` of `Grant` |  |
| `grant($albumKey, $fields)` | Grant someone access to an album (with SecurityType GrantAccess) | `Grant` or null | ContactUri: the contact to grant access to<br>Email: or an email address |
| `packages($albumKey)` | The packages sold from an album | a `Listing` of `Package` |  |
| `applyTemplate($albumKey, $fields)` | Apply an album template's settings to an album | `AppliedAlbumTemplate` or null | AlbumTemplateUri (required): the album template to apply |
| `collectImages($albumKey, $parameters)` | Collect images from other albums into an album (they stay where they are too) | the `Response` array | CollectUris (required): the images to collect (a list of image URIs)<br>Async: true to do it in the background |
| `moveImages($albumKey, $parameters)` | Move images into an album from other albums | the `Response` array | MoveUris (required): the album images to move here (a list of album image URIs)<br>Async: true to do it in the background |
| `sortImages($albumKey, $parameters)` | Reorder an album's images (for SortMethod Position) | the `Response` array | MoveUris (required): the album images to move (a list of URIs)<br>MoveLocation (required): Before or After<br>Uri (required): the album image to move them before or after |
| `deleteImages($albumKey, $parameters)` | Delete several images from an album at once | the `Response` array | DeleteUris (required): the album images to delete (a list of URIs)<br>Async: true to do it in the background |
| `addSamplePhotos($albumKey, $parameters)` | Fill an album with SmugMug's sample photos | the `Response` array |  |
| `uploadFromUri($albumKey, $parameters)` | Have SmugMug fetch a file from an address into an album | the `Response` array | Uri (required): the address SmugMug fetches the file from<br>FileName, Title, Caption, Keywords, Hidden: the new image's details<br>ByteCount, MD5Sum: to check the file<br>Cookie: a cookie to send with the fetch<br>AllowInsecure: allow an http address |
| `uploadFromExternalResource($albumKey, $parameters)` | Import a file from a connected service into an album | the `Response` array | Uri (required): the external resource<br>Id: its id |

| Request | Method | Path |
|---|---|---|
| `Album\GetAlbumRequest` | GET | `/api/v2/album/{albumKey}` |
| `Album\GetAlbumsRequest` | GET | `/api/v2/album/{albumKeys}` |
| `Album\UpdateAlbumRequest` | PATCH | `/api/v2/album/{albumKey}` |
| `Album\DeleteAlbumRequest` | DELETE | `/api/v2/album/{albumKey}` |
| `Album\SearchAlbumsRequest` | GET | `/api/v2/album!search` |
| `Album\GetAlbumImagesRequest` | GET | `/api/v2/album/{albumKey}!images` |
| `Album\GetAlbumPopularMediaRequest` | GET | `/api/v2/album/{albumKey}!popularmedia` |
| `Album\GetAlbumGeoMediaRequest` | GET | `/api/v2/album/{albumKey}!geomedia` |
| `Album\GetAlbumHighlightImageRequest` | GET | `/api/v2/album/{albumKey}!highlightimage` |
| `Album\GetAlbumShareUrisRequest` | GET | `/api/v2/album/{albumKey}!shareuris` |
| `Album\GetAlbumCommentsRequest` | GET | `/api/v2/album/{albumKey}!comments` |
| `Album\CreateAlbumCommentRequest` | POST | `/api/v2/album/{albumKey}!comments` |
| `Album\GetAlbumDownloadsRequest` | GET | `/api/v2/album/{albumKey}!download` |
| `Album\CreateAlbumDownloadRequest` | POST | `/api/v2/album/{albumKey}!download` |
| `Album\UpdateAlbumDownloadRequest` | PUT | `/api/v2/album/{albumKey}!download` |
| `Album\DeleteAlbumDownloadRequest` | DELETE | `/api/v2/album/{albumKey}!download` |
| `Album\GetAlbumPricesRequest` | GET | `/api/v2/album/{albumKey}!prices` |
| `Album\GetAlbumPricelistExclusionsRequest` | GET | `/api/v2/album/{albumKey}!pricelistexclusions` |
| `Album\GetAlbumGrantsRequest` | GET | `/api/v2/album/{albumKey}!grants` |
| `Album\CreateAlbumGrantRequest` | POST | `/api/v2/album/{albumKey}!grants` |
| `Album\GetAlbumPackagesRequest` | GET | `/api/v2/album/{albumKey}!packages` |
| `Album\ApplyAlbumTemplateRequest` | POST | `/api/v2/album/{albumKey}!applyalbumtemplate` |
| `Album\CollectAlbumImagesRequest` | POST | `/api/v2/album/{albumKey}!collectimages` |
| `Album\MoveAlbumImagesRequest` | POST | `/api/v2/album/{albumKey}!moveimages` |
| `Album\SortAlbumImagesRequest` | POST | `/api/v2/album/{albumKey}!sortimages` |
| `Album\DeleteAlbumImagesRequest` | POST | `/api/v2/album/{albumKey}!deleteimages` |
| `Album\AddAlbumSamplePhotosRequest` | POST | `/api/v2/album/{albumKey}!addsamplephotos` |
| `Album\UploadAlbumImageFromUriRequest` | POST | `/api/v2/album/{albumKey}!uploadfromuri` |
| `Album\UploadAlbumImageFromExternalResourceRequest` | POST | `/api/v2/album/{albumKey}!uploadfromexternalresource` |

## Album images

`SmugMug::albumImages()`: images as they are in one album.

| Call | Does | Returns | Parameters |
|---|---|---|---|
| `get($albumKey, $imageKey)` | An image as it is in one album | `AlbumImage` or null |  |
| `find($albumKey, $imageKey)` | As `get()`, null when SmugMug has no such object | `AlbumImage` or null | |
| `update($albumKey, $imageKey, $fields)` | Change an image's details | `AlbumImage` or null | Title, Caption<br>Keywords: semicolon-separated, or KeywordArray: a list<br>Latitude, Longitude, Altitude: where it was taken<br>Hidden: hide it from visitors |
| `delete($albumKey, $imageKey)` | Remove an image from an album (deleting it unless it is collected elsewhere) | true |  |
| `download($albumKey, $imageKey)` | Where to download an album image's original | `DownloadUrl` or null |  |
| `metadata($albumKey, $imageKey)` | An album image's EXIF, IPTC and XMP metadata | `ImageMetadata` or null |  |
| `pricelistExclusions($albumKey, $imageKey)` | The pricelist products that may not be bought for an album image | `PricelistExclusions` or null |  |
| `shareUris($albumKey, $imageKey)` | The links to share an album image | `ShareUris` or null |  |

| Request | Method | Path |
|---|---|---|
| `AlbumImage\GetAlbumImageRequest` | GET | `/api/v2/album/{albumKey}/image/{imageKey}` |
| `AlbumImage\UpdateAlbumImageRequest` | PATCH | `/api/v2/album/{albumKey}/image/{imageKey}` |
| `AlbumImage\DeleteAlbumImageRequest` | DELETE | `/api/v2/album/{albumKey}/image/{imageKey}` |
| `AlbumImage\GetAlbumImageDownloadRequest` | GET | `/api/v2/album/{albumKey}/image/{imageKey}!download` |
| `AlbumImage\GetAlbumImageMetadataRequest` | GET | `/api/v2/album/{albumKey}/image/{imageKey}!metadata` |
| `AlbumImage\GetAlbumImagePricelistExclusionsRequest` | GET | `/api/v2/album/{albumKey}/image/{imageKey}!pricelistexclusions` |
| `AlbumImage\GetAlbumImageShareUrisRequest` | GET | `/api/v2/album/{albumKey}/image/{imageKey}!shareuris` |

## Images

`SmugMug::images()`: images: their details, sizes, metadata and edits, one at a time or in bulk.

| Call | Does | Returns | Parameters |
|---|---|---|---|
| `get($imageKey)` | An image, by key (with its serial, as in jPPKD2c-1) | `Image` or null |  |
| `find($imageKey)` | As `get()`, null when SmugMug has no such object | `Image` or null | |
| `many(array $imageKeys)` | Several images at once (multi-get), by key | a `Listing` of `Image` |  |
| `update($imageKey, $fields)` | Change an image's details | `Image` or null | Title, Caption<br>Keywords: semicolon-separated, or KeywordArray: a list<br>Latitude, Longitude, Altitude: where it was taken<br>Hidden: hide it from visitors |
| `delete($imageKey)` | Delete an image from every album it is in | true |  |
| `search()` | Search images | a `Listing` of `Image` | Scope (required): the URI of the user, node or album to search in<br>Text: the text to search for<br>Keywords: only images with these keywords<br>SortMethod: DateTaken, DateUploaded or Popular<br>SortDirection: Ascending or Descending<br>Relevance: sort by relevance first<br>DateTakenStart, DateTakenEnd, DateUploadedStart, DateUploadedEnd: ISO 8601 dates<br>Type: Image or Video |
| `albums($imageKey)` | The albums an image is in | a `Listing` of `Album` |  |
| `comments($imageKey)` | The comments on an image | a `Listing` of `Comment` |  |
| `addComment($imageKey, $fields)` | Comment on an image | `Comment` or null | Comment (required): the text<br>Name, Email, Link: who is commenting, for a comment without a token<br>Rating: a rating<br>SocialID, ServiceID: a social sign-in |
| `download($imageKey)` | Where to download an image's original | `DownloadUrl` or null |  |
| `metadata($imageKey)` | An image's EXIF, IPTC and XMP metadata | `ImageMetadata` or null |  |
| `pricelistExclusions($imageKey)` | The pricelist products that may not be bought for an image | `PricelistExclusions` or null |  |
| `prices($imageKey)` | What an image sells for | a `Listing` of `CatalogSkuPrice` | Currency: USD, CAD, GBP, EUR, AUD, JPY, CHF, NZD, SEK or HKD |
| `sizes($imageKey)` | The URL of every size of an image | `ImageSizes` or null |  |
| `sizeDetails($imageKey)` | Every size of an image in detail, with which may be used | `ImageSizeDetails` or null | PrefetchSizes: the sizes to have SmugMug prepare now |
| `largest($imageKey)` | The largest size of an image the credentials may see | `ImageSize` or null | MaxSize: the largest size to consider, such as ImageSizeX2Large |
| `size($imageKey, Size $size)` | One size of an image: Tiny, Thumb, Small, Medium, Large, XLarge, X2Large to X5Large, 4K, 5K or Original | `ImageSize` or null |  |
| `customSize($imageKey)` | An image fitted to a size of your own | `ImageSize` or null | width (required), height (required): the box to fit the image in, in pixels<br>fill: crop to fill the box |
| `pointOfInterest($imageKey)` | The point crops of an image centre on | `PointOfInterest` or null |  |
| `pointOfInterestCrops($imageKey)` | The crops of an image around its point of interest | a `Listing` of `PointOfInterestCrops` |  |
| `regions($imageKey)` | The regions marked on an image | a `Listing` of `Region` |  |
| `addRegion($imageKey, $fields)` | Mark a region on an image | `Region` or null | X (required), Y (required), Width (required), Height (required): the region, as fractions of the image<br>Type (required): Face, AF Point, Crop or Cover Image |
| `copy($imageKey, $parameters)` | Copy an image | the `Response` array | AlbumUri: the album to copy the image into |
| `colorPreview($imageKey)` | An image's size with a colour effect applied | `ColorPreview` or null | Color: Auto, Tan, Sepia, B/W or Negative |
| `color($imageKey, $parameters)` | Apply a colour effect to an image | the `Response` array | Color (required): Auto, Tan, Sepia, B/W or Negative |
| `crop($imageKey, $parameters)` | Crop an image | the `Response` array | TopLeftX (required), TopLeftY (required), Width (required), Height (required): the crop, in pixels of the original<br>ThumbOnly (required): true to crop only the thumbnail |
| `rotate($imageKey, $parameters)` | Rotate or flip an image | the `Response` array | Degrees (required): 0, 90, 180 or 270<br>Flip (required): None, Horizontal or Vertical |
| `watermark($imageKey, $parameters)` | Watermark an image | the `Response` array | WatermarkUri: the watermark to apply<br>Mode: Add or Remove |
| `removeWatermark($imageKey)` | Remove an image's watermark | true |  |
| `rotateMany($parameters)` | Rotate or flip several images | the `Response` array | RotateUris (required): the images (a list of URIs)<br>Degrees (required): 0, 90, 180 or 270<br>Flip (required): None, Horizontal or Vertical |
| `cropMany($parameters)` | Crop several images the same way | the `Response` array | CropUris (required): the images (a list of URIs)<br>TopLeftX (required), TopLeftY (required), Width (required), Height (required): the crop<br>ThumbOnly (required): true to crop only the thumbnails |
| `colorMany($parameters)` | Apply a colour effect to several images | the `Response` array | ColorUris (required): the images (a list of URIs)<br>Color (required): Auto, Tan, Sepia, B/W or Negative |
| `watermarkMany($parameters)` | Watermark several images | the `Response` array | WatermarkUris (required): the images (a list of URIs)<br>WatermarkUri: the watermark to apply<br>Mode: Add or Remove |
| `removeWatermarkMany($parameters)` | Remove the watermark from several images | the `Response` array | WatermarkUris (required): the images (a list of URIs) |
| `addKeywords($parameters)` | Add keywords to several images | the `Response` array | ImageUris (required): the images (a list of URIs)<br>Keywords (required): the keywords to add (semicolon-separated)<br>Async: true to do it in the background |
| `removeKeywords($parameters)` | Remove keywords from several images | the `Response` array | ImageUris (required): the images (a list of URIs)<br>Keywords (required): the keywords to remove (semicolon-separated)<br>Async: true to do it in the background |

| Request | Method | Path |
|---|---|---|
| `Image\GetImageRequest` | GET | `/api/v2/image/{imageKey}` |
| `Image\GetImagesRequest` | GET | `/api/v2/image/{imageKeys}` |
| `Image\UpdateImageRequest` | PATCH | `/api/v2/image/{imageKey}` |
| `Image\DeleteImageRequest` | DELETE | `/api/v2/image/{imageKey}` |
| `Image\SearchImagesRequest` | GET | `/api/v2/image!search` |
| `Image\GetImageAlbumsRequest` | GET | `/api/v2/image/{imageKey}!albums` |
| `Image\GetImageCommentsRequest` | GET | `/api/v2/image/{imageKey}!comments` |
| `Image\CreateImageCommentRequest` | POST | `/api/v2/image/{imageKey}!comments` |
| `Image\GetImageDownloadRequest` | GET | `/api/v2/image/{imageKey}!download` |
| `Image\GetImageMetadataRequest` | GET | `/api/v2/image/{imageKey}!metadata` |
| `Image\GetImagePricelistExclusionsRequest` | GET | `/api/v2/image/{imageKey}!pricelistexclusions` |
| `Image\GetImagePricesRequest` | GET | `/api/v2/image/{imageKey}!prices` |
| `Image\GetImageSizesRequest` | GET | `/api/v2/image/{imageKey}!sizes` |
| `Image\GetImageSizeDetailsRequest` | GET | `/api/v2/image/{imageKey}!sizedetails` |
| `Image\GetLargestImageRequest` | GET | `/api/v2/image/{imageKey}!largestimage` |
| `Image\GetImageSizeRequest` | GET | `/api/v2/image/{imageKey}!size{size}` |
| `Image\GetImageCustomSizeRequest` | GET | `/api/v2/image/{imageKey}!sizecustom` |
| `Image\GetImagePointOfInterestRequest` | GET | `/api/v2/image/{imageKey}!pointofinterest` |
| `Image\GetImagePointOfInterestCropsRequest` | GET | `/api/v2/image/{imageKey}!poicrops` |
| `Image\GetImageRegionsRequest` | GET | `/api/v2/image/{imageKey}!regions` |
| `Image\CreateImageRegionRequest` | POST | `/api/v2/image/{imageKey}!regions` |
| `Image\CopyImageRequest` | POST | `/api/v2/image/{imageKey}!copy` |
| `Image\GetImageColorPreviewRequest` | GET | `/api/v2/image/{imageKey}!color` |
| `Image\ColorImageRequest` | POST | `/api/v2/image/{imageKey}!color` |
| `Image\CropImageRequest` | POST | `/api/v2/image/{imageKey}!crop` |
| `Image\RotateImageRequest` | POST | `/api/v2/image/{imageKey}!rotate` |
| `Image\WatermarkImageRequest` | POST | `/api/v2/image/{imageKey}!watermark` |
| `Image\RemoveImageWatermarkRequest` | DELETE | `/api/v2/image/{imageKey}!watermark` |
| `Image\RotateImagesRequest` | POST | `/api/v2/image!rotate` |
| `Image\CropImagesRequest` | POST | `/api/v2/image!crop` |
| `Image\ColorImagesRequest` | POST | `/api/v2/image!color` |
| `Image\WatermarkImagesRequest` | POST | `/api/v2/image!watermark` |
| `Image\RemoveImagesWatermarkRequest` | DELETE | `/api/v2/image!watermark` |
| `Image\AddImageKeywordsRequest` | POST | `/api/v2/image!addkeywords` |
| `Image\RemoveImageKeywordsRequest` | POST | `/api/v2/image!removekeywords` |

## Nodes

`SmugMug::nodes()`: nodes: the folders, albums and pages of a site, by node id.

| Call | Does | Returns | Parameters |
|---|---|---|---|
| `get($nodeId)` | A node (folder, album or page), by id | `Node` or null |  |
| `find($nodeId)` | As `get()`, null when SmugMug has no such object | `Node` or null | |
| `many(array $nodeIds)` | Several nodes at once (multi-get), by id | a `Listing` of `Node` |  |
| `update($nodeId, $fields)` | Change a node | `Node` or null | Name, UrlName, Description, Keywords (a list)<br>Privacy: Public, Unlisted or Private<br>SecurityType: None, Password or GrantAccess<br>Password, PasswordHint<br>SmugSearchable: No, Local, LocalUser, Yes or Inherit from User<br>WorldSearchable: No, HomeOnly, Yes or Inherit from User<br>SortMethod: SortIndex, Name, DateAdded or DateModified<br>SortDirection: Ascending or Descending<br>HighlightImageUri, CoverImageUri, ShowCoverImage: the images that represent it<br>HideOwner<br>AlbumTemplateUri<br>AutoRename: rename instead of failing when the UrlName is taken<br>ResetUrlName: make the UrlName from the Name again |
| `delete($nodeId)` | Delete a node and everything in it (to the trash) | true |  |
| `children($nodeId)` | The folders, albums and pages in a folder node | a `Listing` of `Node` | Type: All, Folder Album Page, Folder, Album, Page or System Album<br>SortMethod: DateAdded, DateModified, Name or Organizer<br>SortDirection: Ascending or Descending |
| `create($nodeId, $fields)` | Create a folder, album or page in a folder node | `Node` or null | Type (required): Album, Folder or Page<br>Name (required)<br>UrlName: the address part (made from the Name when left out)<br>Description, Keywords (a list)<br>Privacy: Public, Unlisted or Private<br>SecurityType, Password, PasswordHint<br>SmugSearchable, WorldSearchable<br>SortMethod, SortDirection<br>HighlightImageUri, CoverImageUri, ShowCoverImage, HideOwner<br>AlbumTemplateUri: an album template to create an album from<br>AutoRename: rename instead of failing when the UrlName is taken |
| `parent($nodeId)` | The folder node a node is in | `Node` or null |  |
| `parents($nodeId)` | Every folder node above a node, nearest first | a `Listing` of `Node` |  |
| `cover($nodeId)` | A node's cover image | `Image` or null |  |
| `highlightImage($nodeId)` | The image that represents a node | `Image` or null |  |
| `comments($nodeId)` | The comments on a node | a `Listing` of `Comment` |  |
| `addComment($nodeId, $fields)` | Comment on a node | `Comment` or null | Comment (required): the text<br>Name, Email, Link: who is commenting, for a comment without a token<br>Rating: a rating<br>SocialID, ServiceID: a social sign-in |
| `grants($nodeId)` | Who has been granted access to a node | a `Listing` of `Grant` |  |
| `grant($nodeId, $fields)` | Grant someone access to a node | `Grant` or null | ContactUri: the contact to grant access to<br>Email: or an email address |
| `pricelist($nodeId)` | The pricelist mapped to a node | `Pricelist` or null |  |
| `setPricelist($nodeId, $fields)` | Map a pricelist to a node | `Pricelist` or null | PricelistUri (required): the pricelist to map |
| `removePricelist($nodeId)` | Remove the pricelist mapped to a node | true |  |
| `design($nodeId)` | The page design of a node | `PageDesign` or null |  |
| `createSelection($nodeId, $parameters)` | Create a selection (an album of chosen images) under a node | the `Response` array | AlbumImageUris: the album images to select<br>SearchText, SearchSort: or a search<br>Limit: the most images to select |
| `createClientDownload($nodeId, $parameters)` | Create a client download for a node | the `Response` array | Limit: the most images to include |
| `move($nodeId, $parameters)` | Move nodes into a folder node | the `Response` array | MoveUris (required): the nodes to move into this folder node (a list of URIs)<br>AutoRename: rename instead of failing on a clash<br>Async: true to do it in the background |
| `search()` | Search nodes | a `Listing` of `Node` | Scope (required): the URI of the user or node to search in<br>Text: the text to search for<br>SortMethod: Rank or LastUpdated<br>SortDirection: Ascending or Descending |
| `updateAlbums($parameters)` | Change the settings of several albums at once | the `Response` array | NodeUris (required): the album nodes to change (a list of URIs)<br>any album setting (Privacy, Watermark, AllowDownloads, LargestSize, Printable, PricelistUri and so on): the value to give every one |

| Request | Method | Path |
|---|---|---|
| `Node\GetNodeRequest` | GET | `/api/v2/node/{nodeId}` |
| `Node\GetNodesRequest` | GET | `/api/v2/node/{nodeIds}` |
| `Node\UpdateNodeRequest` | PATCH | `/api/v2/node/{nodeId}` |
| `Node\DeleteNodeRequest` | DELETE | `/api/v2/node/{nodeId}` |
| `Node\GetNodeChildrenRequest` | GET | `/api/v2/node/{nodeId}!children` |
| `Node\CreateNodeRequest` | POST | `/api/v2/node/{nodeId}!children` |
| `Node\GetNodeParentRequest` | GET | `/api/v2/node/{nodeId}!parent` |
| `Node\GetNodeParentsRequest` | GET | `/api/v2/node/{nodeId}!parents` |
| `Node\GetNodeCoverImageRequest` | GET | `/api/v2/node/{nodeId}!cover` |
| `Node\GetNodeHighlightImageRequest` | GET | `/api/v2/highlight/node/{nodeId}` |
| `Node\GetNodeCommentsRequest` | GET | `/api/v2/node/{nodeId}!comments` |
| `Node\CreateNodeCommentRequest` | POST | `/api/v2/node/{nodeId}!comments` |
| `Node\GetNodeGrantsRequest` | GET | `/api/v2/node/{nodeId}!grants` |
| `Node\CreateNodeGrantRequest` | POST | `/api/v2/node/{nodeId}!grants` |
| `Node\GetNodePricelistRequest` | GET | `/api/v2/node/{nodeId}!pricelist` |
| `Node\SetNodePricelistRequest` | POST | `/api/v2/node/{nodeId}!pricelist` |
| `Node\RemoveNodePricelistRequest` | DELETE | `/api/v2/node/{nodeId}!pricelist` |
| `Node\GetNodePageDesignRequest` | GET | `/api/v2/node/{nodeId}!nodedesign` |
| `Node\CreateNodeSelectionRequest` | POST | `/api/v2/node/{nodeId}!createselection` |
| `Node\CreateNodeClientDownloadRequest` | POST | `/api/v2/node/{nodeId}!createclientdownload` |
| `Node\MoveNodesRequest` | POST | `/api/v2/node/{nodeId}!movenodes` |
| `Node\SearchNodesRequest` | GET | `/api/v2/node!search` |
| `Node\UpdateNodeAlbumsRequest` | POST | `/api/v2/node!updatealbums` |

## Folders (legacy)

`SmugMug::folders()`: folders by path (the folder api, which nodes replace).

| Call | Does | Returns | Parameters |
|---|---|---|---|
| `get($nickname, $path)` | A folder, by user and path ('' for the root) | `Folder` or null | FolderID: the folder's id instead of its path |
| `find($nickname, $path)` | As `get()`, null when SmugMug has no such object | `Folder` or null | |
| `update($nickname, $path, $fields)` | Change a folder | `Folder` or null | Name, UrlName, Description, Keywords<br>Privacy: Public, Unlisted or Private<br>SecurityType, Password, PasswordHint<br>SmugSearchable, WorldSearchable<br>SortMethod: SortIndex, Name, DateAdded or DateModified<br>SortDirection<br>HighlightImageUri<br>AutoRename |
| `delete($nickname, $path)` | Delete a folder and everything in it | true |  |
| `byId($nodeId)` | A folder, by its node id | `Folder` or null |  |
| `albums($nickname, $path)` | The albums in a folder | a `Listing` of `Album` | FolderID |
| `createAlbum($nickname, $path, $fields)` | Create an album in a folder | `Album` or null | Name (required), UrlName (required)<br>and any album setting (Privacy, SecurityType, Description, Keywords, SortMethod, Watermark and so on)<br>AlbumTemplateUri: an album template to start from<br>AutoRename |
| `albumList($nickname, $path)` | Every album under a folder, as a flat list | a `Listing` of `AlbumListItem` | FolderID |
| `folders($nickname, $path)` | The folders in a folder | a `Listing` of `Folder` | FolderID |
| `createFolder($nickname, $path, $fields)` | Create a folder in a folder | `Folder` or null | Name (required), UrlName (required)<br>Description, Keywords<br>Privacy, SecurityType, Password, PasswordHint<br>SmugSearchable, WorldSearchable<br>SortMethod, SortDirection<br>HighlightImageUri<br>AutoRename |
| `folderList($nickname, $path)` | Every folder under a folder, as a flat list | a `Listing` of `FolderListItem` | FolderID |
| `pages($nickname, $path)` | The pages in a folder | a `Listing` of `Page` | FolderID |
| `createPage($nickname, $path, $fields)` | Create a page in a folder | `Page` or null | Name (required), UrlName (required)<br>Privacy, SecurityType, Password, PasswordHint<br>AutoRename |
| `search($nickname, $path)` | Search the folders under a folder | a `Listing` of `Folder` | Text: the text to search for<br>SortMethod: Rank or LastUpdated<br>SortDirection: Ascending or Descending |
| `highlightImage($nickname, $path)` | The image that represents a folder | `Image` or null | FolderID |
| `parent($nickname, $path)` | The folder a folder is in | `Folder` or null | FolderID |
| `parents($nickname, $path)` | Every folder above a folder | a `Listing` of `Folder` | FolderID |
| `size($nickname, $path)` | How much a folder holds | `Size` or null | FolderID<br>depth: 1 (this folder) or MAX (everything under it) |
| `grants($nickname, $path)` | Who has been granted access to a folder | a `Listing` of `Grant` |  |
| `grant($nickname, $path, $fields)` | Grant someone access to a folder | `Grant` or null | ContactUri: the contact to grant access to<br>Email: or an email address |
| `moveAlbums($nickname, $path, $parameters)` | Move albums into a folder | the `Response` array | MoveUris (required): the albums to move here (a list of URIs)<br>AutoRename |
| `moveFolders($nickname, $path, $parameters)` | Move folders into a folder | the `Response` array | MoveUris (required): the folders to move here (a list of URIs)<br>AutoRename |
| `movePages($nickname, $path, $parameters)` | Move pages into a folder | the `Response` array | MoveUris (required): the pages to move here (a list of URIs)<br>AutoRename |
| `sortAlbums($nickname, $path, $parameters)` | Reorder a folder's albums | the `Response` array | MoveUris (required): the albums to move (a list of URIs)<br>MoveLocation (required): Before or After<br>Uri (required): the album to move them before or after |
| `sortFolders($nickname, $path, $parameters)` | Reorder a folder's folders | the `Response` array | MoveUris (required): the folders to move (a list of URIs)<br>MoveLocation (required): Before or After<br>Uri (required): the folder to move them before or after |
| `sortPages($nickname, $path, $parameters)` | Reorder a folder's pages | the `Response` array | MoveUris (required): the pages to move (a list of URIs)<br>MoveLocation (required): Before or After<br>Uri (required): the page to move them before or after |
| `createAlbumFromTemplate($nickname, $path, $fields)` | Create an album in a folder from an album template | `Album` or null | AlbumTemplateUri (required): the template<br>Name, UrlName, Description, Keywords, DownloadPassword, SecurityType, ThemeUri, UploadKey<br>AutoRename |

| Request | Method | Path |
|---|---|---|
| `Folder\GetFolderRequest` | GET | `/api/v2/folder/user/{nickname}{path*}` |
| `Folder\UpdateFolderRequest` | PATCH | `/api/v2/folder/user/{nickname}{path*}` |
| `Folder\DeleteFolderRequest` | DELETE | `/api/v2/folder/user/{nickname}{path*}` |
| `Folder\GetFolderByIdRequest` | GET | `/api/v2/folder/id/{nodeId}` |
| `Folder\GetFolderAlbumsRequest` | GET | `/api/v2/folder/user/{nickname}{path*}!albums` |
| `Folder\CreateFolderAlbumRequest` | POST | `/api/v2/folder/user/{nickname}{path*}!albums` |
| `Folder\GetFolderAlbumListRequest` | GET | `/api/v2/folder/user/{nickname}{path*}!albumlist` |
| `Folder\GetFolderFoldersRequest` | GET | `/api/v2/folder/user/{nickname}{path*}!folders` |
| `Folder\CreateFolderRequest` | POST | `/api/v2/folder/user/{nickname}{path*}!folders` |
| `Folder\GetFolderFolderListRequest` | GET | `/api/v2/folder/user/{nickname}{path*}!folderlist` |
| `Folder\GetFolderPagesRequest` | GET | `/api/v2/folder/user/{nickname}{path*}!pages` |
| `Folder\CreateFolderPageRequest` | POST | `/api/v2/folder/user/{nickname}{path*}!pages` |
| `Folder\SearchFolderRequest` | GET | `/api/v2/folder/user/{nickname}{path*}!search` |
| `Folder\GetFolderHighlightImageRequest` | GET | `/api/v2/folder/user/{nickname}{path*}!highlightimage` |
| `Folder\GetFolderParentRequest` | GET | `/api/v2/folder/user/{nickname}{path*}!parent` |
| `Folder\GetFolderParentsRequest` | GET | `/api/v2/folder/user/{nickname}{path*}!parents` |
| `Folder\GetFolderSizeRequest` | GET | `/api/v2/folder/user/{nickname}{path*}!size` |
| `Folder\GetFolderGrantsRequest` | GET | `/api/v2/folder/user/{nickname}{path*}!grants` |
| `Folder\CreateFolderGrantRequest` | POST | `/api/v2/folder/user/{nickname}{path*}!grants` |
| `Folder\MoveFolderAlbumsRequest` | POST | `/api/v2/folder/user/{nickname}{path*}!movealbums` |
| `Folder\MoveFoldersRequest` | POST | `/api/v2/folder/user/{nickname}{path*}!movefolders` |
| `Folder\MoveFolderPagesRequest` | POST | `/api/v2/folder/user/{nickname}{path*}!movepages` |
| `Folder\SortFolderAlbumsRequest` | POST | `/api/v2/folder/user/{nickname}{path*}!sortalbums` |
| `Folder\SortFoldersRequest` | POST | `/api/v2/folder/user/{nickname}{path*}!sortfolders` |
| `Folder\SortFolderPagesRequest` | POST | `/api/v2/folder/user/{nickname}{path*}!sortpages` |
| `Folder\CreateFolderAlbumFromTemplateRequest` | POST | `/api/v2/folder/user/{nickname}{path*}!albumfromalbumtemplate` |

## Comments

`SmugMug::comments()`: comments.

| Call | Does | Returns | Parameters |
|---|---|---|---|
| `get($commentId)` | A comment, by id | `Comment` or null |  |
| `create($fields)` | Post a comment | `Comment` or null | Comment (required): the text<br>Name, Email, Link, Rating, SocialID, ServiceID |
| `lookup()` | The comments on an album or image by its numeric id, including those awaiting approval | a `Listing` of `Comment` | type (required): Album or Image<br>typeid (required): the album's or image's numeric id<br>status (required): All, Pending or Approved |

| Request | Method | Path |
|---|---|---|
| `Comment\GetCommentRequest` | GET | `/api/v2/comment/{commentId}` |
| `Comment\CreateCommentRequest` | POST | `/api/v2/comment` |
| `Comment\LookupCommentsRequest` | GET | `/api/v2/comment!lookuptype` |

## Watermarks

`SmugMug::watermarks()`: watermarks.

| Call | Does | Returns | Parameters |
|---|---|---|---|
| `forUser($nickname)` | A user's watermarks | a `Listing` of `Watermark` | Unavailable: include the watermarks the plan no longer allows |
| `create($fields)` | Create a watermark | `Watermark` or null | Name (required)<br>ImageID (required): the numeric id of the image to use<br>ImageUri: or its URI<br>Dissolve: opacity, 0 to 100<br>Pinned: Center, TopLeft, TopRight, BottomLeft, BottomRight, Tile, Top, Left, Right or Bottom<br>SmallestSize: Th, S, M, L or XL, the smallest size watermarked |
| `get($watermarkId)` | A watermark, by id | `Watermark` or null |  |
| `update($watermarkId, $fields)` | Change a watermark | `Watermark` or null | Name, ImageID, ImageKey, Dissolve, Pinned, SmallestSize |
| `delete($watermarkId)` | Delete a watermark | true |  |
| `metadata($watermarkId)` | The text of a text watermark | `WatermarkMetadata` or null |  |
| `updateMetadata($watermarkId, $fields)` | Change the text of a text watermark | `WatermarkMetadata` or null | TextContents, TextColor, TextBackgroundColor, TextFont, TextSize |

| Request | Method | Path |
|---|---|---|
| `Watermark\GetUserWatermarksRequest` | GET | `/api/v2/user/{nickname}!watermarks` |
| `Watermark\CreateWatermarkRequest` | POST | `/api/v2/watermark` |
| `Watermark\GetWatermarkRequest` | GET | `/api/v2/watermark/{watermarkId}` |
| `Watermark\UpdateWatermarkRequest` | PATCH | `/api/v2/watermark/{watermarkId}` |
| `Watermark\DeleteWatermarkRequest` | DELETE | `/api/v2/watermark/{watermarkId}` |
| `Watermark\GetWatermarkMetadataRequest` | GET | `/api/v2/watermark/{watermarkId}!metadata` |
| `Watermark\UpdateWatermarkMetadataRequest` | PATCH | `/api/v2/watermark/{watermarkId}!metadata` |

## Printmarks

`SmugMug::printmarks()`: printmarks.

| Call | Does | Returns | Parameters |
|---|---|---|---|
| `forUser($nickname)` | A user's printmarks | a `Listing` of `Printmark` | Unavailable: include the printmarks the plan no longer allows |
| `create($fields)` | Create a printmark | `Printmark` or null | Name<br>ImageUri, ImageID or ImageKey: the image to print<br>Dissolve: opacity<br>Gap: the margin<br>Location: where on the print<br>Scale: its size |
| `get($printmarkId)` | A printmark, by id | `Printmark` or null |  |

| Request | Method | Path |
|---|---|---|
| `Printmark\GetUserPrintmarksRequest` | GET | `/api/v2/user/{nickname}!printmarks` |
| `Printmark\CreatePrintmarkRequest` | POST | `/api/v2/printmark` |
| `Printmark\GetPrintmarkRequest` | GET | `/api/v2/printmark/{printmarkId}` |

## Album templates

`SmugMug::albumTemplates()`: album templates.

| Call | Does | Returns | Parameters |
|---|---|---|---|
| `forUser($nickname)` | A user's album templates | a `Listing` of `AlbumTemplate` | SortBy: Date Created<br>SortOrder: Ascending or Descending |
| `create($nickname, $fields)` | Create an album template | `AlbumTemplate` or null | Name (required)<br>and any album setting (Privacy, Watermark, AllowDownloads, SortMethod and so on)<br>IsDefault: use it for new albums |
| `get($albumTemplateId)` | An album template, by id | `AlbumTemplate` or null |  |
| `update($albumTemplateId, $fields)` | Change an album template | `AlbumTemplate` or null | Name, IsDefault, and any album setting |
| `delete($albumTemplateId)` | Delete an album template | true |  |

| Request | Method | Path |
|---|---|---|
| `AlbumTemplate\GetUserAlbumTemplatesRequest` | GET | `/api/v2/user/{nickname}!albumtemplates` |
| `AlbumTemplate\CreateAlbumTemplateRequest` | POST | `/api/v2/user/{nickname}!albumtemplates` |
| `AlbumTemplate\GetAlbumTemplateRequest` | GET | `/api/v2/albumtemplate/{albumTemplateId}` |
| `AlbumTemplate\UpdateAlbumTemplateRequest` | PATCH | `/api/v2/albumtemplate/{albumTemplateId}` |
| `AlbumTemplate\DeleteAlbumTemplateRequest` | DELETE | `/api/v2/albumtemplate/{albumTemplateId}` |

## Display templates

`SmugMug::templates()`: album display templates.

| Call | Does | Returns | Parameters |
|---|---|---|---|
| `all()` | Every album display template | a `Listing` of `Template` |  |
| `get($templateId)` | An album display template, by id | `Template` or null |  |

| Request | Method | Path |
|---|---|---|
| `Template\GetTemplatesRequest` | GET | `/api/v2/template!templates` |
| `Template\GetTemplateRequest` | GET | `/api/v2/template/{templateId}` |

## Themes

`SmugMug::themes()`: album themes (legacy smugmug).

| Call | Does | Returns | Parameters |
|---|---|---|---|
| `all()` | Every album theme (legacy SmugMug) | a `Listing` of `Theme` |  |
| `get($themeId)` | An album theme, by id | `Theme` or null |  |

| Request | Method | Path |
|---|---|---|
| `Theme\GetThemesRequest` | GET | `/api/v2/theme!themes` |
| `Theme\GetThemeRequest` | GET | `/api/v2/theme/{themeId}` |

## Pages

`SmugMug::pages()`: pages.

| Call | Does | Returns | Parameters |
|---|---|---|---|
| `get($pageKey)` | A page, by key | `Page` or null |  |

| Request | Method | Path |
|---|---|---|
| `Page\GetPageRequest` | GET | `/api/v2/page/{pageKey}` |

## Downloads

`SmugMug::downloads()`: prepared album downloads.

| Call | Does | Returns | Parameters |
|---|---|---|---|
| `get($downloadId)` | A prepared download, by id | `Download` or null |  |

| Request | Method | Path |
|---|---|---|
| `Download\GetDownloadRequest` | GET | `/api/v2/download/{downloadId}` |

## Guides

`SmugMug::guides()`: smugmug's in-app guides.

| Call | Does | Returns | Parameters |
|---|---|---|---|
| `get($guideId)` | One of SmugMug's guides, by id | `Guide` or null |  |
| `userState($guideId)` | Whether a user has completed a guide | `GuideState` or null | NickName: the user |

| Request | Method | Path |
|---|---|---|
| `Guide\GetGuideRequest` | GET | `/api/v2/guide/{guideId}` |
| `Guide\GetGuideUserStateRequest` | GET | `/api/v2/guide/{guideId}!userstate` |

## Edit status

`SmugMug::status()`: the progress of asynchronous image edits.

| Call | Does | Returns | Parameters |
|---|---|---|---|
| `rotation($statusId)` | The progress of a rotation | `ImageEditStatus` or null |  |
| `crop($statusId)` | The progress of a crop | `ImageEditStatus` or null |  |
| `color($statusId)` | The progress of a colour effect | `ImageEditStatus` or null |  |
| `watermark($statusId)` | The progress of a watermarking | `ImageEditStatus` or null |  |

| Request | Method | Path |
|---|---|---|
| `Status\GetImageRotateStatusRequest` | GET | `/api/v2/status/image/rotate/{statusId}` |
| `Status\GetImageCropStatusRequest` | GET | `/api/v2/status/image/crop/{statusId}` |
| `Status\GetImageColorStatusRequest` | GET | `/api/v2/status/image/color/{statusId}` |
| `Status\GetImageWatermarkStatusRequest` | GET | `/api/v2/status/image/watermark/{statusId}` |

## Contacts

`SmugMug::contacts()`: contacts, and the access granted to them.

| Call | Does | Returns | Parameters |
|---|---|---|---|
| `forUser($nickname)` | A user's contacts | a `Listing` of `Contact` | SortBy: Name or Email<br>SortOrder: ASC or DESC<br>Email: only this address<br>Search: the text to search for |
| `create($nickname, $fields)` | Add a contact | `Contact` or null | PrimaryEmail (required)<br>Name<br>AllowDuplicateEmail: allow an address another contact has |
| `get($contactId)` | A contact, by id | `Contact` or null |  |
| `update($contactId, $fields)` | Change a contact | `Contact` or null | Name, PrimaryEmail, AllowDuplicateEmail |
| `delete($contactId)` | Delete a contact | true |  |
| `emails($contactId)` | A contact's email addresses | a `Listing` of `ContactEmail` |  |
| `email($contactId, $email)` | One of a contact's email addresses | `ContactEmail` or null |  |
| `grants($contactId)` | The access granted to a contact | a `Listing` of `Grant` |  |

| Request | Method | Path |
|---|---|---|
| `Contact\GetUserContactsRequest` | GET | `/api/v2/user/{nickname}!contacts` |
| `Contact\CreateContactRequest` | POST | `/api/v2/user/{nickname}!contacts` |
| `Contact\GetContactRequest` | GET | `/api/v2/contact/{contactId}` |
| `Contact\UpdateContactRequest` | PATCH | `/api/v2/contact/{contactId}` |
| `Contact\DeleteContactRequest` | DELETE | `/api/v2/contact/{contactId}` |
| `Contact\GetContactEmailsRequest` | GET | `/api/v2/contact/{contactId}!emails` |
| `Contact\GetContactEmailRequest` | GET | `/api/v2/contact/{contactId}/email/{email}` |
| `Contact\GetContactGrantsRequest` | GET | `/api/v2/contact/{contactId}!grants` |

## Trash

`SmugMug::trash()`: deleted albums, folders and pages, and recovering them.

| Call | Does | Returns | Parameters |
|---|---|---|---|
| `albums($nickname)` | The albums in a user's trash | a `Listing` of `DeletedNode` |  |
| `folders($nickname)` | The folders in a user's trash | a `Listing` of `DeletedNode` |  |
| `pages($nickname)` | The pages in a user's trash | a `Listing` of `DeletedNode` |  |
| `album($nodeId)` | A deleted album, by node id | `DeletedNode` or null |  |
| `folder($nodeId)` | A deleted folder, by node id | `DeletedNode` or null |  |
| `page($nodeId)` | A deleted page, by node id | `DeletedNode` or null |  |
| `recoverAlbum($nodeId, $fields)` | Recover a deleted album | `Recovered` or null | FolderUri: the folder to recover it into (where it was by default)<br>IncludeChildren: recover its images too |
| `recoverFolder($nodeId, $fields)` | Recover a deleted folder | `Recovered` or null | FolderUri: the folder to recover it into<br>IncludeChildren: recover what was in it too |
| `recoverPage($nodeId, $fields)` | Recover a deleted page | `Recovered` or null | FolderUri: the folder to recover it into |

| Request | Method | Path |
|---|---|---|
| `Trash\GetUserDeletedAlbumsRequest` | GET | `/api/v2/user/{nickname}!deletedalbums` |
| `Trash\GetUserDeletedFoldersRequest` | GET | `/api/v2/user/{nickname}!deletedfolders` |
| `Trash\GetUserDeletedPagesRequest` | GET | `/api/v2/user/{nickname}!deletedpages` |
| `Trash\GetDeletedAlbumRequest` | GET | `/api/v2/deleted/album/{nodeId}` |
| `Trash\GetDeletedFolderRequest` | GET | `/api/v2/deleted/folder/{nodeId}` |
| `Trash\GetDeletedPageRequest` | GET | `/api/v2/deleted/page/{nodeId}` |
| `Trash\RecoverDeletedAlbumRequest` | POST | `/api/v2/deleted/album/{nodeId}!recover` |
| `Trash\RecoverDeletedFolderRequest` | POST | `/api/v2/deleted/folder/{nodeId}!recover` |
| `Trash\RecoverDeletedPageRequest` | POST | `/api/v2/deleted/page/{nodeId}!recover` |

## Catalogue

`SmugMug::catalog()`: smugmug's catalogue of products: vendors, categories, products and skus.

| Call | Does | Returns | Parameters |
|---|---|---|---|
| `vendor($vendorId)` | A vendor of the catalogue, such as SmugMug | `CatalogVendor` or null |  |
| `categories($vendorId)` | A vendor's product categories | a `Listing` of `CatalogCategory` |  |
| `category($vendorId, $categoryId)` | A product category | `CatalogCategory` or null |  |
| `subCategories($vendorId, $categoryId)` | A category's subcategories | a `Listing` of `CatalogCategory` |  |
| `subCategory($vendorId, $categoryId, $subCategoryId)` | A product subcategory | `CatalogCategory` or null |  |
| `vendorProducts($vendorId)` | A vendor's products | a `Listing` of `CatalogProduct` | ShowDisabled: include disabled products |
| `printVendor($vendorId)` | A print lab | `CatalogVendor` or null | ApplyColorCorrection<br>PriceType: Base, Pro or Wholesale |
| `printVendorProducts($vendorId)` | A print lab's products | a `Listing` of `CatalogProduct` | ShowDisabled: include disabled products |
| `printVendorPrices($vendorId)` | A print lab's prices | a `Listing` of `CatalogSkuPrice` | Currency<br>ApplyColorCorrection<br>PriceType: Base, Pro or Wholesale |
| `product($productId)` | A product, by id | `CatalogProduct` or null |  |
| `productAddons($productId)` | The add-ons sold with a product | a `Listing` of `CatalogSku` |  |
| `productOptions($productId)` | The options (SKUs) of a product | a `Listing` of `CatalogSku` |  |
| `sku($skuId)` | A SKU, by id | `CatalogSku` or null |  |
| `skuPrice($skuId)` | What a SKU costs | `CatalogSkuPrice` or null | Album, Image: the album or image it is sold from<br>Currency<br>ApplyColorCorrection<br>PriceType: Base, Pro or Wholesale |
| `buy($skuId, $parameters)` | Add a SKU to the cart | the `Response` array | Quantity (required)<br>Image, Album: the image (and album) to buy it for<br>CropLeft, CropTop, CropWidth, CropHeight, CropRotation, CropAcknowledged: the crop<br>Source |
| `batchAdd($skuId, $fields)` | Add a SKU to the cart for many images at once | `BatchAddResult` or null | Images (required): the images (a list of URIs)<br>Quantity (required)<br>FilterImagesOverTheCartItemLimit |
| `skuType($skuType, $typeCode)` | A type of SKU, by type and code | `CatalogSkuType` or null |  |

| Request | Method | Path |
|---|---|---|
| `Catalog\GetCatalogVendorRequest` | GET | `/api/v2/catalog/vendor/{vendorId}` |
| `Catalog\GetCatalogCategoriesRequest` | GET | `/api/v2/catalog/vendor/{vendorId}!categories` |
| `Catalog\GetCatalogCategoryRequest` | GET | `/api/v2/catalog/vendor/{vendorId}/category/{categoryId}` |
| `Catalog\GetCatalogSubCategoriesRequest` | GET | `/api/v2/catalog/vendor/{vendorId}/category/{categoryId}!subcategories` |
| `Catalog\GetCatalogSubCategoryRequest` | GET | `/api/v2/catalog/vendor/{vendorId}/category/{categoryId}/{subCategoryId}` |
| `Catalog\GetCatalogVendorProductsRequest` | GET | `/api/v2/catalog/vendor/{vendorId}!products` |
| `Catalog\GetCatalogPrintVendorRequest` | GET | `/api/v2/catalog/printvendor/{vendorId}` |
| `Catalog\GetCatalogPrintVendorProductsRequest` | GET | `/api/v2/catalog/printvendor/{vendorId}!products` |
| `Catalog\GetCatalogPrintVendorPricesRequest` | GET | `/api/v2/catalog/printvendor/{vendorId}!prices` |
| `Catalog\GetCatalogProductRequest` | GET | `/api/v2/catalog/product/{productId}` |
| `Catalog\GetCatalogProductAddonsRequest` | GET | `/api/v2/catalog/product/{productId}!addons` |
| `Catalog\GetCatalogProductOptionsRequest` | GET | `/api/v2/catalog/product/{productId}!options` |
| `Catalog\GetCatalogSkuRequest` | GET | `/api/v2/catalog/sku/{skuId}` |
| `Catalog\GetCatalogSkuPriceRequest` | GET | `/api/v2/catalog/sku/{skuId}!price` |
| `Catalog\BuyCatalogSkuRequest` | POST | `/api/v2/catalog/sku/{skuId}!buy` |
| `Catalog\BatchAddCatalogSkuRequest` | POST | `/api/v2/catalog/sku/{skuId}!batchadd` |
| `Catalog\GetCatalogSkuTypeRequest` | GET | `/api/v2/catalog/skutype/{skuType}{typeCode*}` |

## Pricelists

`SmugMug::pricelists()`: pricelists.

| Call | Does | Returns | Parameters |
|---|---|---|---|
| `get($pricelistKey)` | A pricelist, by key | `Pricelist` or null |  |
| `update($pricelistKey, $fields)` | Change a pricelist | `Pricelist` or null | Name, ColorCorrection, Default, Currency |
| `prices($pricelistKey)` | The prices on a pricelist | a `Listing` of `PricelistSkuPrice` | Currency |
| `price($pricelistKey, $skuId)` | The price of one SKU on a pricelist | `PricelistSkuPrice` or null | Currency |

| Request | Method | Path |
|---|---|---|
| `Pricelist\GetPricelistRequest` | GET | `/api/v2/pricelist/{pricelistKey}` |
| `Pricelist\UpdatePricelistRequest` | PATCH | `/api/v2/pricelist/{pricelistKey}` |
| `Pricelist\GetPricelistPricesRequest` | GET | `/api/v2/pricelist/{pricelistKey}!prices` |
| `Pricelist\GetPricelistSkuPriceRequest` | GET | `/api/v2/pricelist/{pricelistKey}/skuprice/{skuId}` |

## Coupons

`SmugMug::coupons()`: coupons.

| Call | Does | Returns | Parameters |
|---|---|---|---|
| `forUser($nickname)` | A user's coupons | a `Listing` of `Coupon` | SortBy: Name, Date Created, Coupon Code or Most Used<br>SortOrder: Ascending or Descending<br>ShowExpired |
| `create($nickname, $fields)` | Create a coupon | `Coupon` or null | CouponCode (required), Title (required), Currency (required), ValidFrom (required), DiscountType (required) (amount, percent, cost or shipping)<br>DiscountAmount, MaxDiscount, MinPurchase, MaxItems, MaxUses<br>ValidThru<br>Shipping, InternationalShipping, ShowBanner<br>Description<br>Nodes, CatalogRestrictions: what it applies to |
| `get($nickname, $couponCode)` | A coupon, by code | `Coupon` or null |  |
| `update($nickname, $couponCode, $fields)` | Change a coupon | `Coupon` or null | any of the fields a coupon is created with |
| `albums($nickname, $couponCode)` | The albums a coupon applies to | a `Listing` of `Album` |  |
| `nodes($nickname, $couponCode)` | The nodes a coupon applies to | a `Listing` of `Node` |  |
| `categoryRestrictions($nickname, $couponCode)` | The categories a coupon is restricted to | a `Listing` of `CatalogCategory` |  |
| `subCategoryRestrictions($nickname, $couponCode)` | The subcategories a coupon is restricted to | a `Listing` of `CatalogCategory` |  |
| `productRestrictions($nickname, $couponCode)` | The products a coupon is restricted to | a `Listing` of `CatalogProduct` |  |
| `skuRestrictions($nickname, $couponCode)` | The SKUs a coupon is restricted to | a `Listing` of `CatalogSku` |  |

| Request | Method | Path |
|---|---|---|
| `Coupon\GetUserCouponsRequest` | GET | `/api/v2/user/{nickname}!coupons` |
| `Coupon\CreateCouponRequest` | POST | `/api/v2/user/{nickname}!coupons` |
| `Coupon\GetCouponRequest` | GET | `/api/v2/coupon/user/{nickname}/code/{couponCode}` |
| `Coupon\UpdateCouponRequest` | PATCH | `/api/v2/coupon/user/{nickname}/code/{couponCode}` |
| `Coupon\GetCouponAlbumsRequest` | GET | `/api/v2/coupon/user/{nickname}/code/{couponCode}!albums` |
| `Coupon\GetCouponNodesRequest` | GET | `/api/v2/coupon/user/{nickname}/code/{couponCode}!nodes` |
| `Coupon\GetCouponCategoryRestrictionsRequest` | GET | `/api/v2/coupon/user/{nickname}/code/{couponCode}!catalogcategoryrestrictions` |
| `Coupon\GetCouponSubCategoryRestrictionsRequest` | GET | `/api/v2/coupon/user/{nickname}/code/{couponCode}!catalogsubcategoryrestrictions` |
| `Coupon\GetCouponProductRestrictionsRequest` | GET | `/api/v2/coupon/user/{nickname}/code/{couponCode}!catalogproductrestrictions` |
| `Coupon\GetCouponSkuRestrictionsRequest` | GET | `/api/v2/coupon/user/{nickname}/code/{couponCode}!catalogskurestrictions` |

## Orders

`SmugMug::orders()`: orders the user placed as a buyer.

| Call | Does | Returns | Parameters |
|---|---|---|---|
| `forUser($nickname)` | The orders a user placed | a `Listing` of `Order` |  |
| `get($orderId)` | An order, by id | `Order` or null |  |
| `items($orderId)` | The lines of an order | a `Listing` of `OrderItem` |  |
| `item($orderId, $itemId)` | One line of an order | `OrderItem` or null |  |
| `itemContents($orderId, $itemId)` | The images on a line of an order | a `Listing` of `OrderItemContent` |  |
| `itemParent($orderId, $itemId)` | The order a line belongs to | `Order` or null |  |
| `itemType($orderId, $itemId)` | The SKU a line is for | `CatalogSku` or null |  |
| `content($orderId, $contentId)` | One image on an order | `OrderItemContent` or null |  |
| `contentType($orderId, $contentId)` | The image an order's content is | `Image` or null |  |
| `shipments($orderId)` | The shipments of an order | a `Listing` of `Shipment` |  |
| `shipment($orderId, $shipmentId)` | One shipment of an order | `Shipment` or null |  |
| `shipmentItems($orderId, $shipmentId)` | The lines in a shipment | a `Listing` of `OrderItem` |  |
| `trackingNumbers($orderId, $shipmentId)` | The tracking numbers of a shipment | a `Listing` of `TrackingNumbers` |  |
| `billingAddress($orderId)` | An order's billing address | `Address` or null |  |
| `shippingAddress($orderId)` | An order's shipping address | `Address` or null |  |
| `vendors($orderId)` | The vendors fulfilling an order | a `Listing` of `OrderVendor` |  |
| `vendorShipments($orderId, $vendor)` | The shipments from one vendor of an order | a `Listing` of `Shipment` |  |
| `addToCart($orderId, $parameters)` | Add an order's items to the cart again | the `Response` array |  |

| Request | Method | Path |
|---|---|---|
| `Order\GetUserOrdersRequest` | GET | `/api/v2/user/{nickname}!orders` |
| `Order\GetOrderRequest` | GET | `/api/v2/order/{orderId}` |
| `Order\GetOrderItemsRequest` | GET | `/api/v2/order/{orderId}!items` |
| `Order\GetOrderItemRequest` | GET | `/api/v2/order/{orderId}/item/{itemId}` |
| `Order\GetOrderItemContentsRequest` | GET | `/api/v2/order/{orderId}/item/{itemId}!contents` |
| `Order\GetOrderItemParentRequest` | GET | `/api/v2/order/{orderId}/item/{itemId}!parent` |
| `Order\GetOrderItemTypeRequest` | GET | `/api/v2/order/{orderId}/item/{itemId}!type` |
| `Order\GetOrderItemContentRequest` | GET | `/api/v2/order/{orderId}/content/{contentId}` |
| `Order\GetOrderItemContentTypeRequest` | GET | `/api/v2/order/{orderId}/content/{contentId}!type` |
| `Order\GetOrderShipmentsRequest` | GET | `/api/v2/order/{orderId}!shipments` |
| `Order\GetOrderShipmentRequest` | GET | `/api/v2/order/{orderId}/shipment/{shipmentId}` |
| `Order\GetOrderShipmentItemsRequest` | GET | `/api/v2/order/{orderId}/shipment/{shipmentId}!items` |
| `Order\GetOrderShipmentTrackingNumbersRequest` | GET | `/api/v2/order/{orderId}/shipment/{shipmentId}!trackingnumbers` |
| `Order\GetOrderBillingAddressRequest` | GET | `/api/v2/order/{orderId}!billingaddress` |
| `Order\GetOrderShippingAddressRequest` | GET | `/api/v2/order/{orderId}!shippingaddress` |
| `Order\GetOrderVendorsRequest` | GET | `/api/v2/order/{orderId}!vendors` |
| `Order\GetOrderVendorShipmentsRequest` | GET | `/api/v2/order/{orderId}/vendor/{vendor}!shipments` |
| `Order\AddOrderToCartRequest` | POST | `/api/v2/order/{orderId}!addtocart` |

## Sales

`SmugMug::sales()`: orders buyers placed with the user, and the payments for them.

| Call | Does | Returns | Parameters |
|---|---|---|---|
| `forUser($nickname)` | The orders buyers placed with a user | a `Listing` of `SalesOrder` |  |
| `proPayments($nickname)` | The payments SmugMug made to a user for their sales | a `Listing` of `ProPayment` |  |
| `get($nickname, $orderId)` | A sales order, by id | `SalesOrder` or null |  |
| `update($nickname, $orderId, $fields)` | Release, or change the delivery of, a sales order | `SalesOrder` or null | Action: ReleaseProDelay, PersonalDeliveryOff, BoutiquePackagingOn or BoutiquePackagingOff |
| `items($nickname, $orderId)` | The lines of a sales order | a `Listing` of `SalesOrderItem` |  |
| `item($nickname, $orderId, $itemId)` | One line of a sales order | `SalesOrderItem` or null |  |
| `updateItem($nickname, $orderId, $itemId, $fields)` | Turn colour correction on or off for a line | `SalesOrderItem` or null | Action: ColorCorrectionOn or ColorCorrectionOff |
| `itemContents($nickname, $orderId, $itemId)` | The images on a line of a sales order | a `Listing` of `SalesOrderItemContent` |  |
| `itemParent($nickname, $orderId, $itemId)` | The sales order a line belongs to | `SalesOrder` or null |  |
| `itemType($nickname, $orderId, $itemId)` | The SKU a line is for | `CatalogSku` or null |  |
| `content($nickname, $orderId, $contentId)` | One image on a sales order | `SalesOrderItemContent` or null |  |
| `updateContent($nickname, $orderId, $contentId, $fields)` | Change how an image on a sales order is cropped | `SalesOrderItemContent` or null | Action: AdjustCrop, DisableCrop, DisablePrintmark or ResetCrop<br>CropLeft, CropTop, CropWidth, CropHeight, CropRotation, CropAcknowledged: the crop for AdjustCrop |
| `contentType($nickname, $orderId, $contentId)` | The image a sales order's content is | `Image` or null |  |
| `shipments($nickname, $orderId)` | The shipments of a sales order | a `Listing` of `Shipment` |  |
| `shipment($nickname, $orderId, $shipmentId)` | One shipment of a sales order | `Shipment` or null |  |
| `billingAddress($nickname, $orderId)` | A sales order's billing address | `Address` or null |  |
| `shippingAddress($nickname, $orderId)` | A sales order's shipping address | `Address` or null |  |
| `proSalePayments($nickname, $orderId)` | The payments to the user for a sales order | a `Listing` of `ProPayment` |  |
| `proTaxPayments($nickname, $orderId)` | The tax paid to the user for a sales order | a `Listing` of `ProTaxPayment` |  |

| Request | Method | Path |
|---|---|---|
| `Sales\GetUserSalesOrdersRequest` | GET | `/api/v2/user/{nickname}!salesorders` |
| `Sales\GetUserProPaymentsRequest` | GET | `/api/v2/user/{nickname}!propayments` |
| `Sales\GetSalesOrderRequest` | GET | `/api/v2/sales/{nickname}/order/{orderId}` |
| `Sales\UpdateSalesOrderRequest` | PATCH | `/api/v2/sales/{nickname}/order/{orderId}` |
| `Sales\GetSalesOrderItemsRequest` | GET | `/api/v2/sales/{nickname}/order/{orderId}!items` |
| `Sales\GetSalesOrderItemRequest` | GET | `/api/v2/sales/{nickname}/order/{orderId}/item/{itemId}` |
| `Sales\UpdateSalesOrderItemRequest` | PATCH | `/api/v2/sales/{nickname}/order/{orderId}/item/{itemId}` |
| `Sales\GetSalesOrderItemContentsRequest` | GET | `/api/v2/sales/{nickname}/order/{orderId}/item/{itemId}!contents` |
| `Sales\GetSalesOrderItemParentRequest` | GET | `/api/v2/sales/{nickname}/order/{orderId}/item/{itemId}!parent` |
| `Sales\GetSalesOrderItemTypeRequest` | GET | `/api/v2/sales/{nickname}/order/{orderId}/item/{itemId}!type` |
| `Sales\GetSalesOrderItemContentRequest` | GET | `/api/v2/sales/{nickname}/order/{orderId}/content/{contentId}` |
| `Sales\UpdateSalesOrderItemContentRequest` | PATCH | `/api/v2/sales/{nickname}/order/{orderId}/content/{contentId}` |
| `Sales\GetSalesOrderItemContentTypeRequest` | GET | `/api/v2/sales/{nickname}/order/{orderId}/content/{contentId}!type` |
| `Sales\GetSalesOrderShipmentsRequest` | GET | `/api/v2/sales/{nickname}/order/{orderId}!shipments` |
| `Sales\GetSalesOrderShipmentRequest` | GET | `/api/v2/sales/{nickname}/order/{orderId}/shipment/{shipmentId}` |
| `Sales\GetSalesOrderBillingAddressRequest` | GET | `/api/v2/sales/{nickname}/order/{orderId}!billingaddress` |
| `Sales\GetSalesOrderShippingAddressRequest` | GET | `/api/v2/sales/{nickname}/order/{orderId}!shippingaddress` |
| `Sales\GetSalesOrderProSalePaymentsRequest` | GET | `/api/v2/sales/{nickname}/order/{orderId}!prosalepayments` |
| `Sales\GetSalesOrderProTaxPaymentsRequest` | GET | `/api/v2/sales/{nickname}/order/{orderId}!protaxpayments` |

## Designs

`SmugMug::designs()`: site designs, page designs, widgets and skins.

| Call | Does | Returns | Parameters |
|---|---|---|---|
| `forUser($nickname)` | A user's site designs | a `Listing` of `SiteDesign` |  |
| `available($nickname)` | The site designs a user can start from | a `Listing` of `SiteDesign` |  |
| `root($nickname)` | A user's active site design | `SiteDesign` or null |  |
| `get($siteDesignKey)` | A site design, by key | `SiteDesign` or null |  |
| `update($siteDesignKey, $fields)` | Change where a site design appears in the design picker | `SiteDesign` or null | DesignPickerOrder, DesignPickerGroupName (Showcase Gallery, Slideshow, Photo Grid, Photo Collage, Photo Column or Spotlight Photo) |
| `activate($siteDesignKey, $parameters)` | Make a site design the active one | the `Response` array |  |
| `clone($siteDesignKey, $parameters)` | Copy a site design | the `Response` array |  |
| `publish($siteDesignKey, $parameters)` | Publish a site design's unpublished changes | the `Response` array |  |
| `faviconImage($siteDesignKey)` | A site design's favicon | `Image` or null |  |
| `highlightImage($siteDesignKey)` | A site design's highlight image | `Image` or null |  |
| `folderDesign($siteDesignKey)` | The design of a site design's folder pages | `PageDesign` or null |  |
| `galleryDesign($siteDesignKey)` | The design of a site design's gallery pages | `PageDesign` or null |  |
| `globalDesign($siteDesignKey)` | The design every page of a site design shares | `PageDesign` or null |  |
| `homepageDesign($siteDesignKey)` | The design of a site design's homepage | `PageDesign` or null |  |
| `pageDesign($pageDesignId)` | A page design, by id | `PageDesign` or null |  |
| `publishedPageDesign($pageDesignId)` | The published version of a page design | `PageDesign` or null |  |
| `publishedWidgets($pageDesignId)` | The widgets on the published version of a page design | a `Listing` of `Widget` |  |
| `workingPageDesign($pageDesignId)` | The working (unpublished) version of a page design | `PageDesign` or null |  |
| `updateWorkingPageDesign($pageDesignId, $fields)` | Change the working version of a page design | `PageDesign` or null | BackgroundType: none, photo, slideshow or video<br>BackgroundImageUri, BackgroundAlbumUri<br>BackgroundSlideshowSource: album, keyword, recent or selectedImages |
| `workingWidgets($pageDesignId)` | The widgets on the working version of a page design | a `Listing` of `Widget` |  |
| `widget($widgetId)` | A profile widget, by id | `Widget` or null |  |
| `publishedWidget($widgetId)` | The published settings of a profile widget | `ProfileWidget` or null |  |
| `workingWidget($widgetId)` | The working settings of a profile widget | `ProfileWidget` or null |  |
| `updateWorkingWidget($widgetId, $fields)` | Change the working settings of a profile widget | `ProfileWidget` or null | MarginTop, MarginRight, MarginBottom, MarginLeft<br>IconColor: default, primary, accent or official<br>IconType: default, square or circle<br>NewTab, ShowCoverPhoto, ShowEmail, ShowIcons, ShowProfilePhoto |
| `publishedSkin($userSkinId)` | The published version of a site skin | `UserSkin` or null |  |
| `workingSkin($userSkinId)` | The working version of a site skin | `UserSkin` or null |  |

| Request | Method | Path |
|---|---|---|
| `Design\GetUserSiteDesignsRequest` | GET | `/api/v2/user/{nickname}!sitedesigns` |
| `Design\GetUserAvailableSiteDesignsRequest` | GET | `/api/v2/user/{nickname}!availabledesigns` |
| `Design\GetUserRootSiteDesignRequest` | GET | `/api/v2/user/{nickname}!rootsitedesign` |
| `Design\GetSiteDesignRequest` | GET | `/api/v2/sitedesign/{siteDesignKey}` |
| `Design\UpdateSiteDesignRequest` | PATCH | `/api/v2/sitedesign/{siteDesignKey}` |
| `Design\ActivateSiteDesignRequest` | POST | `/api/v2/sitedesign/{siteDesignKey}!activate` |
| `Design\CloneSiteDesignRequest` | POST | `/api/v2/sitedesign/{siteDesignKey}!clone` |
| `Design\PublishSiteDesignRequest` | POST | `/api/v2/sitedesign/{siteDesignKey}!publish` |
| `Design\GetSiteDesignFaviconImageRequest` | GET | `/api/v2/sitedesign/{siteDesignKey}!faviconimage` |
| `Design\GetSiteDesignHighlightImageRequest` | GET | `/api/v2/sitedesign/{siteDesignKey}!highlightimage` |
| `Design\GetSiteDesignFolderDesignRequest` | GET | `/api/v2/sitedesign/{siteDesignKey}!folderdesign` |
| `Design\GetSiteDesignGalleryDesignRequest` | GET | `/api/v2/sitedesign/{siteDesignKey}!gallerydesign` |
| `Design\GetSiteDesignGlobalDesignRequest` | GET | `/api/v2/sitedesign/{siteDesignKey}!globaldesign` |
| `Design\GetSiteDesignHomepageDesignRequest` | GET | `/api/v2/sitedesign/{siteDesignKey}!homepagedesign` |
| `Design\GetPageDesignRequest` | GET | `/api/v2/pagedesign/{pageDesignId}` |
| `Design\GetPublishedPageDesignRequest` | GET | `/api/v2/pagedesign/{pageDesignId}/published` |
| `Design\GetPublishedPageDesignWidgetsRequest` | GET | `/api/v2/pagedesign/{pageDesignId}/published!widgets` |
| `Design\GetWorkingPageDesignRequest` | GET | `/api/v2/pagedesign/{pageDesignId}/working` |
| `Design\UpdateWorkingPageDesignRequest` | PATCH | `/api/v2/pagedesign/{pageDesignId}/working` |
| `Design\GetWorkingPageDesignWidgetsRequest` | GET | `/api/v2/pagedesign/{pageDesignId}/working!widgets` |
| `Design\GetProfileWidgetRequest` | GET | `/api/v2/widget/profile/{widgetId}` |
| `Design\GetPublishedProfileWidgetRequest` | GET | `/api/v2/widget/profile/{widgetId}/published` |
| `Design\GetWorkingProfileWidgetRequest` | GET | `/api/v2/widget/profile/{widgetId}/working` |
| `Design\UpdateWorkingProfileWidgetRequest` | PATCH | `/api/v2/widget/profile/{widgetId}/working` |
| `Design\GetPublishedUserSkinRequest` | GET | `/api/v2/userskin/{userSkinId}/published` |
| `Design\GetWorkingUserSkinRequest` | GET | `/api/v2/userskin/{userSkinId}/working` |

## Account

`SmugMug::account()`: api keys, plans, subscriptions, invoices and the library.

| Call | Does | Returns | Parameters |
|---|---|---|---|
| `apiKeys($nickname)` | A user's API keys | a `Listing` of `ApiKey` |  |
| `createApiKey($nickname, $fields)` | Apply for an API key | `ApiKey` or null | Name (required)<br>Type (required): Application, Plug-In, Service, Toy, Other or MCP Server / Agentic AI<br>Platform (required): Windows, Macintosh, Linux, Mobile, All or Other<br>UsedFor (required): Non-Commercial or Commercial<br>Description (required)<br>AcceptTermsOfService (required): true<br>AppUrl, CallbackUrl |
| `apiKey($apiKeyId)` | An API key, by id | `ApiKey` or null |  |
| `updateApiKey($apiKeyId, $fields)` | Change an API key's name or addresses | `ApiKey` or null | Name, AppUrl, CallbackUrl |
| `offeredPlans($nickname)` | The plans a user may move to | a `Listing` of `Plan` |  |
| `plan($planKey)` | A plan, by key | `Plan` or null | Currency |
| `planMarketingFeatures($planKey)` | Which of a plan's features the user has used | `PlanMarketingFeatures` or null |  |
| `subscriptions($nickname)` | A user's subscriptions | a `Listing` of `Subscription` |  |
| `subscription($subscriptionKey)` | A subscription, by key | `Subscription` or null |  |
| `updateSubscription($subscriptionKey, $fields)` | Change a subscription | `Subscription` or null | NumberOfSeats, ConsentVersion |
| `cancelSubscription($subscriptionKey, $parameters)` | Cancel a subscription | the `Response` array | Email (required): the account's email address, to confirm |
| `invoices($subscriptionKey)` | A subscription's invoices | a `Listing` of `Invoice` | InvoiceStatus: Draft, Open, Paid, Uncollectible or Void |
| `invoice($invoiceKey)` | An invoice, by key | `Invoice` or null |  |
| `assetLabels($assetKey)` | The labels on a library asset | a `Listing` of `Label` |  |
| `assetComponents($assetKey)` | The files of a library asset | a `Listing` of `Component` |  |
| `assetComponent($assetKey, $componentType, $componentKey)` | One file of a library asset | `Component` or null |  |

| Request | Method | Path |
|---|---|---|
| `Account\GetUserApiKeysRequest` | GET | `/api/v2/user/{nickname}!apikeys` |
| `Account\CreateApiKeyRequest` | POST | `/api/v2/user/{nickname}!apikeys` |
| `Account\GetApiKeyRequest` | GET | `/api/v2/apikey/{apiKeyId}` |
| `Account\UpdateApiKeyRequest` | PATCH | `/api/v2/apikey/{apiKeyId}` |
| `Account\GetUserOfferedPlansRequest` | GET | `/api/v2/user/{nickname}!offeredplans` |
| `Account\GetPlanRequest` | GET | `/api/v2/plan/{planKey}` |
| `Account\GetPlanMarketingFeaturesRequest` | GET | `/api/v2/plan/{planKey}!marketingfeatures` |
| `Account\GetUserSubscriptionsRequest` | GET | `/api/v2/user/{nickname}!subscriptions` |
| `Account\GetSubscriptionRequest` | GET | `/api/v2/subscription/{subscriptionKey}` |
| `Account\UpdateSubscriptionRequest` | PATCH | `/api/v2/subscription/{subscriptionKey}` |
| `Account\CancelSubscriptionRequest` | POST | `/api/v2/subscription/{subscriptionKey}!cancel` |
| `Account\GetSubscriptionInvoicesRequest` | GET | `/api/v2/subscription/{subscriptionKey}!invoices` |
| `Account\GetInvoiceRequest` | GET | `/api/v2/invoice/{invoiceKey}` |
| `Account\GetLibraryAssetLabelsRequest` | GET | `/api/v2/library/asset/{assetKey}!labels` |
| `Account\GetLibraryAssetComponentsRequest` | GET | `/api/v2/library/asset/{assetKey}!components` |
| `Account\GetLibraryAssetComponentRequest` | GET | `/api/v2/library/asset/{assetKey}/component/{componentType}/{componentKey}` |
