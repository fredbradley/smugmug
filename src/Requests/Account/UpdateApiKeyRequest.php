<?php

namespace FredBradley\SmugMug\Requests\Account;

use FredBradley\SmugMug\Data\Account\ApiKey;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\WriteRequest;
use FredBradley\SmugMug\Support\Path;
use JsonSerializable;
use Saloon\Enums\Method;

/**
 * Change an API key's name or addresses: PATCH /api/v2/apikey/{apiKeyId}.
 *
 * Parameters (in the body):
 *  - Name, AppUrl, CallbackUrl
 */
class UpdateApiKeyRequest extends WriteRequest
{
    protected Method $method = Method::PATCH;

    protected ?string $dto = ApiKey::class;

    /**
     * @param  array<mixed>|JsonSerializable  $body  SmugMug's field names and values, or a model.
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $apiKeyId, array|JsonSerializable $body = [], Options|array $options = [])
    {
        $this->setBody($body, $options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/apikey/'.Path::segment($this->apiKeyId);
    }
}
