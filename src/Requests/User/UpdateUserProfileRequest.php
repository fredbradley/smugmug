<?php

namespace FredBradley\SmugMug\Requests\User;

use FredBradley\SmugMug\Data\User\UserProfile;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\WriteRequest;
use FredBradley\SmugMug\Support\Path;
use JsonSerializable;
use Saloon\Enums\Method;

/**
 * Change a user's profile: PATCH /api/v2/user/{nickname}!profile.
 *
 * Parameters (in the body):
 *  - BioText: the biography
 *  - BioImageUri: the URI of the image to use as the bio image
 *  - CoverImageUri: the URI of the image to use as the cover image
 *  - FirstName
 *  - LastName
 *  - DisplayName
 *  - Facebook, Twitter, Instagram, Blogger, FiveHundredPX, Flickr, LinkedIn, Pinterest, Tumblr, Vimeo, Wordpress, YouTube, Custom: links to the user's other sites
 */
class UpdateUserProfileRequest extends WriteRequest
{
    protected Method $method = Method::PATCH;

    protected ?string $dto = UserProfile::class;

    /**
     * @param  array<mixed>|JsonSerializable  $body  SmugMug's field names and values, or a model.
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $nickname, array|JsonSerializable $body = [], Options|array $options = [])
    {
        $this->setBody($body, $options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/user/'.Path::segment($this->nickname).'!profile';
    }
}
