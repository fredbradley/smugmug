<?php

namespace FredBradley\SmugMug\Requests\Account;

use FredBradley\SmugMug\Data\Account\ApiKey;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\WriteRequest;
use FredBradley\SmugMug\Support\Path;
use JsonSerializable;

/**
 * Apply for an API key: POST /api/v2/user/{nickname}!apikeys.
 *
 * Parameters (in the body):
 *  - Name (required)
 *  - Type (required): Application, Plug-In, Service, Toy, Other or MCP Server / Agentic AI
 *  - Platform (required): Windows, Macintosh, Linux, Mobile, All or Other
 *  - UsedFor (required): Non-Commercial or Commercial
 *  - Description (required)
 *  - AcceptTermsOfService (required): true
 *  - AppUrl, CallbackUrl
 */
class CreateApiKeyRequest extends WriteRequest
{
    protected ?string $dto = ApiKey::class;

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
        return '/api/v2/user/'.Path::segment($this->nickname).'!apikeys';
    }
}
