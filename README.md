![Banner](https://banners.beyondco.de/SmugMug.png?theme=dark&packageManager=composer+require&packageName=fredbradley%2Fsmugmug&pattern=architect&style=style_2&description=&md=1&showWatermark=1&fontSize=100px&images=https%3A%2F%2Flaravel.com%2Fimg%2Flogomark.min.svg)

[![Tests](https://github.com/fredbradley/smugmug/actions/workflows/tests.yml/badge.svg?branch=main)](https://github.com/fredbradley/smugmug/actions/workflows/tests.yml?branch=main)
[![Latest version](https://img.shields.io/packagist/v/fredbradley/smugmug)](https://packagist.org/packages/fredbradley/smugmug)
[![PHP version](https://img.shields.io/packagist/php-v/fredbradley/smugmug)](https://packagist.org/packages/fredbradley/smugmug)

A Laravel package for the [SmugMug API v2](https://api.smugmug.com/api/v2/doc), built with
[Saloon](https://docs.saloon.dev). It has a request, a resource method and a typed model for every endpoint SmugMug's
Live API Browser lists: 322 of them. On top of those it covers OAuth 1.0a sign-in, uploads through SmugMug's
uploader, and paging through any list.

The endpoints are grouped as the API groups them:

- people and their content: `users()`, `albums()`, `albumImages()`, `images()`, `nodes()`, `folders()`, `comments()`,
  `contacts()`, `trash()`
- marks and templates: `watermarks()`, `printmarks()`, `albumTemplates()`, `templates()`, `themes()`
- everything else: `pages()`, `downloads()`, `guides()`, `status()`, `catalog()`, `pricelists()`, `coupons()`,
  `orders()`, `sales()`, `designs()`, `account()`, `lookup()` and `uploads()`

Every call is listed in [ENDPOINTS.md](ENDPOINTS.md).

```php
use FredBradley\SmugMug\Facades\SmugMug;
use FredBradley\SmugMug\Upload\Media;

$me = SmugMug::users()->me();                                       // User

foreach (SmugMug::users()->albums($me->NickName)->lazy() as $album) {
    // every album, fetched 100 at a time
}

$images = SmugMug::albums()->images('SJT3DX')->get();               // the first page, a Collection of AlbumImage

$large = SmugMug::images()->sizeDetails('jPPKD2c-1')->ImageSizeLarge['Url'];

SmugMug::nodes()->create($rootNodeId, ['Type' => 'Album', 'Name' => 'Sports Day 2026', 'Privacy' => 'Unlisted']);

SmugMug::uploads()->upload('SJT3DX', Media::fromPath('/photos/finish-line.jpg'), ['Title' => 'Finish line']);
```

## Requirements

- PHP 8.5
- Laravel 12.39 or higher, or Laravel 13
- A SmugMug API key and secret. Apply for one at
  [smugmug.com/app/developer](https://www.smugmug.com/app/developer) while signed in to SmugMug. The key alone reads
  public data. Anything private, and every change, needs a user to sign in with OAuth 1.0a.

## Installation

```bash
composer require fredbradley/smugmug
php artisan smugmug:install
```

The install command publishes `config/smugmug.php`. Set your application's key and secret in `.env`:

```dotenv
SMUGMUG_API_KEY=your-api-key
SMUGMUG_API_SECRET=your-api-secret
```

That is enough to read public data, such as `SmugMug::users()->get('cmac')`. To act as one account (yours, for a
site that shows or manages a single SmugMug account), add that account's access token. [Signing in](#signing-in) shows
how to get one. Anything private needs it, and so does `SmugMug::users()->me()`: without a token it throws a
`LogicException`, because SmugMug can only say who signed a request that was signed.

```dotenv
SMUGMUG_ACCESS_TOKEN=the-access-token
SMUGMUG_ACCESS_TOKEN_SECRET=the-access-token-secret
```

| Setting | Env | Default | What it does |
|---|---|---|---|
| `api_key` | `SMUGMUG_API_KEY` | none | Your application's API key, the OAuth consumer key. Required. |
| `api_secret` | `SMUGMUG_API_SECRET` | none | The key's secret. Signs every OAuth request. |
| `access_token`, `access_token_secret` | `SMUGMUG_ACCESS_TOKEN`, `SMUGMUG_ACCESS_TOKEN_SECRET` | none | The account the default connection acts as. Leave both empty to read public data only. |
| `callback_url` | `SMUGMUG_CALLBACK_URL` | `oob` | Where SmugMug sends a user back after they approve your application. `oob` has SmugMug show them a six-digit code instead. |
| `connections` | | `[]` | Other applications or accounts, for `SmugMug::connection('name')`. |
| `timeouts.connect`, `timeouts.request`, `timeouts.upload` | `SMUGMUG_CONNECT_TIMEOUT`, `SMUGMUG_REQUEST_TIMEOUT`, `SMUGMUG_UPLOAD_TIMEOUT` | 10, 30, 300 | Seconds. |
| `retries.times`, `retries.interval_ms` | `SMUGMUG_RETRIES` | off, 500 | How many times to retry a GET that failed for a reason on SmugMug's side. |
| `cache.enabled`, `cache.store`, `cache.seconds` | `SMUGMUG_CACHE`, `SMUGMUG_CACHE_STORE` | off, default store, 3600 | Caches GET responses. |
| `pagination.per_page` | | 100 | The page size `lazy()` and `all()` fetch with. |

## Signing in

SmugMug uses OAuth 1.0a. The package signs every request with HMAC-SHA1 (RFC 5849), in the `Authorization` header,
which is also the only form SmugMug's uploader accepts. Sign-in has three steps:

1. `SmugMug::oauth()->requestToken()` gets a temporary request token, signed by your application. It lasts five
   minutes and can be used once. Keep it in the session.
2. Send the user to `SmugMug::oauth()->authorizationUrl($requestToken, $access, $permissions)`. SmugMug asks them to
   approve your application, then sends them back to your callback URL with `oauth_token` and `oauth_verifier` (or,
   for `oob`, shows them a six-digit code).
3. `SmugMug::oauth()->accessToken($requestToken, $verifier)` trades the request token and verifier for the user's
   access token and secret. SmugMug's access tokens don't expire. A user can revoke one from their SmugMug account
   settings, after which requests answer 401.

What a token may do is fixed when the user approves it:

| `Access` | What the application sees |
|---|---|
| `Access::Public` | Only what anyone may see. SmugMug's default. |
| `Access::Full` | Everything the user may see: private and unlisted albums, account details, orders. |

| `Permissions` | What the application may do |
|---|---|
| `Permissions::Read` | GET only. SmugMug's default. |
| `Permissions::Add` | Read, plus create: new albums, folders, nodes and comments, and uploads. |
| `Permissions::Modify` | Everything: PATCH and DELETE, moves and sorts, bulk image edits, replacing images. |

### In a web application

```php
use FredBradley\SmugMug\Auth\RequestToken;
use FredBradley\SmugMug\Enums\Access;
use FredBradley\SmugMug\Enums\Permissions;
use FredBradley\SmugMug\Facades\SmugMug;
use Illuminate\Http\Request;

Route::get('/smugmug/connect', function () {
    $requestToken = SmugMug::oauth()->requestToken(route('smugmug.callback'));
    session(['smugmug.request_token' => $requestToken->toArray()]);

    return redirect(SmugMug::oauth()->authorizationUrl($requestToken, Access::Full, Permissions::Modify));
})->middleware('auth');

Route::get('/smugmug/callback', function (Request $request) {
    $requestToken = RequestToken::fromArray(session()->pull('smugmug.request_token'));
    abort_unless(hash_equals($requestToken->token, (string) $request->query('oauth_token')), 403);

    $accessToken = SmugMug::oauth()->accessToken($requestToken, (string) $request->query('oauth_verifier'));

    $request->user()->update([
        'smugmug_token' => $accessToken->token,          // cast both as 'encrypted' on the model
        'smugmug_token_secret' => $accessToken->secret,
    ]);

    return redirect('/')->with('status', 'SmugMug connected.');
})->middleware('auth')->name('smugmug.callback');
```

Then act as that user wherever you need to:

```php
$smugmug = SmugMug::withToken($user->smugmug_token, $user->smugmug_token_secret);

$smugmug->users()->me();
$smugmug->uploads()->upload($albumKey, Media::fromPath($path));
```

`withToken()` returns a new connector with every resource on it. Store tokens as you would passwords: encrypted, and
never logged. A connector, an `OAuth1Authenticator` and an `AccessToken` keep their secrets out of `dump()` and
`print_r()`, and a connector refuses to be serialised, so it can't leak into a queued job's payload. In a queued job,
pass the user's id and build the connector in `handle()`.

### For one account, without a web flow

For a site that only ever uses your own account, get its token once with `oob` and put it in `.env`:

```php
// php artisan tinker
$oauth = SmugMug::oauth();
$requestToken = $oauth->requestToken('oob');
echo $oauth->authorizationUrl($requestToken, Access::Full, Permissions::Modify);
// open the URL, approve, and copy the six-digit code
$token = $oauth->accessToken($requestToken, '123456');
echo $token->token, ' ', $token->secret;     // SMUGMUG_ACCESS_TOKEN and SMUGMUG_ACCESS_TOKEN_SECRET
```

`authorizationUrl()` also takes SmugMug's other authorize options: `['allowThirdPartyLogin' => 1,
'showSignUpButton' => false, 'username' => 'cmac', 'viewportScale' => 0.5]`.

## Usage

### Two ways to call SmugMug

**Resources** give a method for each endpoint, take keys as arguments, and return models. When SmugMug answers with
an error they throw a `SmugMugApiException`:

```php
$album = SmugMug::albums()->get('SJT3DX');                          // Album
$album = SmugMug::albums()->find('nope');                           // null for a 404
$album = SmugMug::albums()->update('SJT3DX', ['Privacy' => 'Unlisted']);
SmugMug::albums()->delete('SJT3DX');                                // true
```

**Requests** are plain Saloon, for when you want the response itself:

```php
use FredBradley\SmugMug\Requests\Album\GetAlbumRequest;

$response = SmugMug::send(new GetAlbumRequest('SJT3DX', ['_expand' => 'HighlightImage']));
$response->status();
$response->json();
$response->dto();   // the same Album the resource gives; throws SmugMugApiException for an error
```

`SmugMug::follow($uri)` fetches any URI SmugMug gives you, such as `$album->uri('AlbumImages')`, as its model.
`SmugMug::get()`, `post()`, `patch()`, `put()` and `delete()` send any path on `api.smugmug.com` and return the
response. Every resource, request and model is listed in [ENDPOINTS.md](ENDPOINTS.md).

### Users

```php
$me = SmugMug::users()->me();                       // the user the token belongs to (needs a token)
$user = SmugMug::users()->get('cmac');
$users = SmugMug::users()->many(['cmac', 'onethumb'])->get();
$profile = SmugMug::users()->profile('cmac');       // UserProfile: BioText, Facebook, Instagram and so on
$root = SmugMug::users()->rootNode('cmac');         // Node: the top of their site
$features = SmugMug::users()->features('cmac');     // Features: what their plan allows

SmugMug::users()->updateProfile('cmac', ['BioText' => 'School photography since 1865.']);
SmugMug::users()->featuredAlbums('cmac')->get();
SmugMug::users()->addFeaturedAlbum('cmac', ['AlbumUri' => '/api/v2/album/SJT3DX']);
```

### Albums and their images

```php
$album = SmugMug::albums()->get('SJT3DX');
$album->Name;               // "Sports Day"
$album->ImageCount;
$album->LastUpdated;        // CarbonImmutable
$album->WebUri;             // its page on SmugMug

$images = SmugMug::albums()->images('SJT3DX')->all();                // every AlbumImage
$popular = SmugMug::albums()->popularMedia('SJT3DX', ['MediaType' => 'Images'])->get();

SmugMug::albums()->update('SJT3DX', [
    'Name' => 'Sports Day 2026',
    'Privacy' => 'Unlisted',           // Public, Unlisted or Private
    'SecurityType' => 'Password',      // None, Password or GrantAccess
    'Password' => 'relay',
    'AllowDownloads' => false,
    'LargestSize' => 'X2Large',
    'SortMethod' => 'Date Taken',
]);

SmugMug::albumImages()->update('SJT3DX', 'jPPKD2c-1', ['Caption' => 'Year 9 relay', 'KeywordArray' => ['relay', 'year 9']]);
SmugMug::albumImages()->delete('SJT3DX', 'jPPKD2c-1');              // removes it from this album
```

### Folders, albums and pages: nodes

New SmugMug organises a site as nodes: folders, albums and pages, each with a node id.

```php
$root = SmugMug::users()->rootNode('cmac');

$children = SmugMug::nodes()->children($root->NodeID, ['Type' => 'Album', 'SortMethod' => 'DateModified'])->get();

$folder = SmugMug::nodes()->create($root->NodeID, ['Type' => 'Folder', 'Name' => 'Archive', 'Privacy' => 'Private']);
$album = SmugMug::nodes()->create($folder->NodeID, ['Type' => 'Album', 'Name' => '2025-26', 'UrlName' => '2025-26']);

SmugMug::nodes()->move($folder->NodeID, ['MoveUris' => ['/api/v2/node/abc123', '/api/v2/node/def456']]);
SmugMug::nodes()->parents($album->NodeID)->get();                    // the breadcrumb, nearest first
SmugMug::nodes()->update($album->NodeID, ['HighlightImageUri' => '/api/v2/image/jPPKD2c-1']);
```

A node of type Album links its album: `SmugMug::follow($node->uri('Album'))`. The older folder API, by path, is
`folders()`: `folders()->get('cmac', 'Travel/2024')`, `folders()->albums('cmac', '')`, `folders()->createAlbum()` and
so on. SmugMug recommends nodes.

`lookup()` finds a folder, album or page by its address:
`lookup()->webUri(['WebUri' => 'https://cmac.smugmug.com/Travel/2024'])` or
`lookup()->urlPath(['nickname' => 'cmac', 'urlpath' => '/Travel/2024'])`. Each returns the model for whatever it
found.

### Images, sizes and downloads

```php
use FredBradley\SmugMug\Enums\Size;

$image = SmugMug::images()->get('jPPKD2c-1');
$image->FileName; $image->OriginalWidth; $image->IsVideo; $image->Latitude;

$sizes = SmugMug::images()->sizes('jPPKD2c-1');                     // ImageSizes: a URL per size
$sizes->LargeImageUrl;
$sizes->ImageUrl4K;                                                 // SmugMug's "4KImageUrl"

$details = SmugMug::images()->sizeDetails('jPPKD2c-1');             // each size's URL, width, height and file size
$thumb = SmugMug::images()->size('jPPKD2c-1', Size::Thumb);         // ImageSize: Url, Width, Height, Size, MD5
$largest = SmugMug::images()->largest('jPPKD2c-1');                 // the largest the credentials may see
$fitted = SmugMug::images()->customSize('jPPKD2c-1', ['width' => 1200, 'height' => 630, 'fill' => true]);

$exif = SmugMug::images()->metadata('jPPKD2c-1');                   // ImageMetadata: Make, Model, Lens, ISO and so on
$original = SmugMug::images()->download('jPPKD2c-1')->Url;
```

### Search

```php
SmugMug::images()->search([
    'Scope' => '/api/v2/user/cmac',     // required: a user, node or album URI
    'Text' => 'relay',
    'SortMethod' => 'DateTaken',
    'DateTakenStart' => '2026-06-01T00:00:00Z',
])->get();

SmugMug::albums()->search(['Scope' => '/api/v2/user/cmac', 'Text' => 'sports'])->get();
SmugMug::nodes()->search(['Scope' => '/api/v2/node/'.$root->NodeID, 'Text' => 'archive'])->get();
SmugMug::users()->searchImages('cmac', ['q' => 'relay'])->get();
```

### Bulk edits

Actions return SmugMug's `Response` as an array. Those that take `Async: true` run in the background and return the
URI of their status, which `status()` reads.

```php
SmugMug::albums()->moveImages('TARGET', ['MoveUris' => ['/api/v2/album/SRC/image/a-0', '/api/v2/album/SRC/image/b-0']]);
SmugMug::albums()->collectImages('TARGET', ['CollectUris' => [...]]);       // copy into another album too
SmugMug::albums()->sortImages('SJT3DX', ['MoveUris' => [...], 'MoveLocation' => 'Before', 'Uri' => '/api/v2/album/SJT3DX/image/c-0']);
SmugMug::albums()->deleteImages('SJT3DX', ['DeleteUris' => [...], 'Async' => true]);

SmugMug::images()->addKeywords(['ImageUris' => [...], 'Keywords' => 'sports day; 2026']);
SmugMug::images()->rotateMany(['RotateUris' => [...], 'Degrees' => 90, 'Flip' => 'None']);
SmugMug::images()->watermarkMany(['WatermarkUris' => [...], 'WatermarkUri' => '/api/v2/watermark/780235']);

SmugMug::nodes()->updateAlbums(['NodeUris' => [...], 'Privacy' => 'Private', 'AllowDownloads' => false]);
```

SmugMug takes some of these lists as one comma-separated string and some as arrays. Pass a PHP list either way; the
package writes it the way each endpoint wants it.

### Uploads

```php
use FredBradley\SmugMug\Upload\Media;

$result = SmugMug::uploads()->upload('SJT3DX', Media::fromPath('/photos/finish.jpg'), [
    'Title' => 'Finish line',
    'Caption' => 'Year 9 relay',
    'Keywords' => ['sports day', 'relay'],
    'Hidden' => false,
]);

$result->ImageUri;          // /api/v2/image/Ab12Cd3-0
$result->imageKey();        // Ab12Cd3-0
$result->URL;               // its page on SmugMug

SmugMug::uploads()->replace('SJT3DX', $result->ImageUri, Media::fromPath('/photos/finish-edited.jpg'));
SmugMug::uploads()->upload('SJT3DX', Media::fromContents($request->file('photo')->get(), 'photo.jpg'));
```

How an upload works:

- **Where it goes.** `upload()` and `replace()` use SmugMug's uploader, `https://upload.smugmug.com/`.
- **What it sends.** The file goes as the body, streamed from disk. Its MD5 goes in `Content-MD5`, so SmugMug
  rejects a truncated upload, and its details go in `X-Smug-*` headers.
- **What you need.** The uploader takes OAuth only, so an upload needs an access token with Add permission (Modify to
  replace). Without one the package refuses before sending.
- **Through the API instead.** `toAlbum()` and `replaceMedia()` send the same file as multipart form data
  (`album!images` and `image!replace`).
- **Large files.** `SMUGMUG_UPLOAD_TIMEOUT` (300 seconds by default) applies to uploads only.

### Watermarks, templates and the rest

```php
SmugMug::watermarks()->forUser('cmac')->get();
SmugMug::watermarks()->create(['Name' => 'School crest', 'ImageID' => 123456, 'Pinned' => 'BottomRight', 'Dissolve' => 40]);
SmugMug::albumTemplates()->create('cmac', ['Name' => 'Events', 'Privacy' => 'Unlisted', 'Watermark' => true]);
SmugMug::comments()->lookup(['type' => 'Album', 'typeid' => 1234, 'status' => 'Pending'])->get();
SmugMug::contacts()->create('cmac', ['PrimaryEmail' => 'parent@example.com', 'Name' => 'A Parent']);
SmugMug::albums()->grant('SJT3DX', ['Email' => 'parent@example.com']);
SmugMug::trash()->albums('cmac')->get();
SmugMug::trash()->recoverAlbum($nodeId, ['IncludeChildren' => true]);
```

There are also selling endpoints (`catalog()`, `pricelists()`, `coupons()`, `orders()`, `sales()`), site designs
(`designs()`), and account endpoints (`account()`: API keys, plans, subscriptions, invoices).
[ENDPOINTS.md](ENDPOINTS.md) lists each call with its parameters.

### Lists and paging

A method for a list returns a `Listing` and sends nothing until you ask:

| Call | Returns |
|---|---|
| `->get()` | One page, as a Collection: the `start` and `count` in the options, or SmugMug's default page. |
| `->page(3, 50)` | Page 3 of 50 items. |
| `->lazy()` | Every item, as a LazyCollection, fetching a page of `smugmug.pagination.per_page` at a time as you iterate. `lazy(500)` sets the page size. |
| `->all()` | Every item, fetched at once. |
| `->first()` | The first item, or null. |
| `->total()` | How many there are (SmugMug's `Pages.Total`). |
| `->paginator()` | The `SmugMugPaginator`, for each page's response. |

`foreach ($listing as $item)` is `lazy()`. Paging uses SmugMug's `start` (from 1) and `count`. It stops when SmugMug
gives no `NextPage`, a page is short or empty, or the total is reached. A request from `SmugMug::send()` pages the same
way with `SmugMug::paginate($request)`.

### Options: expansions, filters and an endpoint's parameters

Every call takes options last, as an array or an `Options`:

```php
use FredBradley\SmugMug\Query\Options;

SmugMug::albums()->images('SJT3DX', Options::make()
    ->filter('ImageKey', 'FileName', 'Caption')         // _filter: only these fields
    ->filterUri('ImageSizes')                           // _filteruri: only these Uris
    ->expand('ImageSizes')                              // _expand: include related objects
    ->count(50)->start(51)                              // paging
    ->with('MediaType', 'Images')                       // the endpoint's own parameters
)->get();

SmugMug::albums()->expand('HighlightImage')->get('SJT3DX');     // the same options for every call on a resource
```

`Options` also has:

- `shortUris()`, for `_shorturis`
- `verbosity(1)`, for `_verbosity`
- `config([...])`, for `_config`, SmugMug's advanced form. For example,
  `['expand' => ['ChildNodes' => ['args' => ['count' => 25]]]]` fetches 25 children with each node.

Lists are sent comma-separated and booleans as `true` and `false`. Expanded objects arrive in the response's
`Expansions`, keyed by URI. Read them from a request's response with `FredBradley\SmugMug\Support\Payload::expansions($response)`.

### Models

Every object is a readonly model with SmugMug's own PascalCase property names and types:

- `$album->AlbumKey`, `$album->LastUpdated` (a `CarbonImmutable`), `$image->KeywordArray`.
- Names SmugMug starts with a digit are moved round: `ImageSizes::$ImageUrl4K` for `4KImageUrl`, `$VideoUrl1920` for
  `1920VideoUrl`. `toArray()` writes SmugMug's names back.
- SmugMug adds fields over time and shows some only to the owner, so a model keeps the fields it has no property for
  in `$extra`.
- `uri('AlbumImages')` reads a related endpoint from the object's `Uris`, and `SmugMug::follow()` fetches it.

`toArray()` and `json_encode()` leave out a model's secrets: `Album::$Password`, `$DownloadPassword`,
`User::$ViewPassword` and `ApiKey::$APIKeySecret`. That way a model that is logged, cached or returned from a route
doesn't carry them. `toArray(withHidden: true)` includes them.

### Writing

`create()`, `update()` and every POST or PATCH take SmugMug's field names as an array, or a model. A PATCH changes only
the fields it carries. When given a model, the package leaves out `Uri`, `Uris`, `WebUri`, `UriDescription` and
`ResponseLevel`, which SmugMug sends but never takes back. SmugMug answers a write with the object as stored, which
the method returns as its model.

### Errors

SmugMug wraps every answer in `{"Response", "Code", "Message"}`. A resource method throws a `SmugMugApiException` for
an error status, and for a 200 whose envelope `Code` says the request failed. A request's `dto()`, `$response->throw()`
and the paginator do the same. `errorMessage()` gives SmugMug's `Message`, and `getResponse()` the response. The
exception's message never includes the query string, which may carry the API key.

| SmugMug answered | Exception |
|---|---|
| 400, 405, 406, 415, 422 | `InvalidRequestException` |
| 401: bad credentials, or a token without the Access or Permissions needed | `UnauthorizedException` |
| 402: the subscription has lapsed | `PaymentRequiredException` |
| 403 | `ForbiddenException` |
| 404 (`find()` returns null instead) | `NotFoundException` |
| 409: the name or URL is taken | `ConflictException` |
| 429 | `RateLimitedException`, whose `retryAfter()` gives the seconds to wait |
| 408, 5xx, or a success that isn't JSON | `ServerErrorException` |
| a 200 whose envelope or uploader `stat` says it failed | `FailedException` |

### Rate limits

SmugMug limits requests per user of each application, and per application for requests without a token. Every
response says where the window stands, and `SmugMug::rateLimit($response)` reads it:

- `remaining`: requests left in the window.
- `resetsAt`: when the window ends.
- `retryAfter`: seconds to wait, on a 429.

`SMUGMUG_RETRIES` retries a GET that got a 429 with exponential backoff. Writes are never retried.

### Several accounts or applications

Add them under `connections` in `config/smugmug.php`, with the same keys as the default, and reach them with
`SmugMug::connection('name')`. For a user of your application, use `SmugMug::withToken($token, $secret)`.
`SmugMug::withoutToken()` reads public data.

### Caching, timeouts and retries

**Caching.** `SMUGMUG_CACHE=true` caches GET responses for `smugmug.cache.seconds` in `SMUGMUG_CACHE_STORE` (the
default cache store when unset).

- There is one entry per application, token and query, so one user never reads what another fetched.
- An answer whose envelope says it failed is not kept.
- What is cached is the objects as the credentials saw them, private ones included, so use a store the application
  trusts.

**Timeouts.** `SMUGMUG_CONNECT_TIMEOUT`, `SMUGMUG_REQUEST_TIMEOUT` and `SMUGMUG_UPLOAD_TIMEOUT` are in seconds.

**Retries.** `SMUGMUG_RETRIES` retries a GET that failed with a connection error, 408, 429 or 5xx, with exponential
backoff from `smugmug.retries.interval_ms`.

### Testing your application

The package is plain Saloon, so fake SmugMug with a `MockClient`:

```php
use FredBradley\SmugMug\Requests\User\GetAuthUserRequest;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;

MockClient::global([
    GetAuthUserRequest::class => MockResponse::make([
        'Response' => ['Locator' => 'User', 'LocatorType' => 'Object', 'User' => ['NickName' => 'cmac']],
        'Code' => 200,
        'Message' => 'Ok',
    ]),
]);

expect(SmugMug::users()->me()->NickName)->toBe('cmac');
```

## Testing

```bash
composer test
```

The suite fakes SmugMug with Saloon's `MockClient`, so nothing reaches the real API. It sends every request class
and every resource method, and checks each one's method, path, body and model. It also checks the OAuth signature of
each request, against the RFC 5849 example as well as SmugMug's own requests. `composer analyse` runs PHPStan, and
`composer format` runs Pint.

## Contributing

Pull requests are welcome. Run `composer format`, `composer analyse` and `composer test` before opening one.

## Security

If you discover a security issue, email code@fredbradley.co.uk rather than opening an issue.

## Credits

- [Fred Bradley](https://github.com/fredbradley)
- [Saloon](https://docs.saloon.dev)

## Licence

MIT.
