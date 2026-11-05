<?php

namespace FredBradley\SmugMug\Requests\Album;

use FredBradley\SmugMug\Data\User\Grant;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\WriteRequest;
use FredBradley\SmugMug\Support\Path;
use JsonSerializable;

/**
 * Grant someone access to an album (with SecurityType GrantAccess): POST /api/v2/album/{albumKey}!grants.
 *
 * Parameters (in the body):
 *  - ContactUri: the contact to grant access to
 *  - Email: or an email address
 */
class CreateAlbumGrantRequest extends WriteRequest
{
    protected ?string $dto = Grant::class;

    /**
     * @param  array<mixed>|JsonSerializable  $body  SmugMug's field names and values, or a model.
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $albumKey, array|JsonSerializable $body = [], Options|array $options = [])
    {
        $this->setBody($body, $options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/album/'.Path::segment($this->albumKey).'!grants';
    }
}
