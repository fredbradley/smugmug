<?php

namespace FredBradley\SmugMug\Requests\Account;

use FredBradley\SmugMug\Data\Account\ApiKey;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ItemRequest;
use FredBradley\SmugMug\Support\Path;

/**
 * An API key, by id: GET /api/v2/apikey/{apiKeyId}.
 */
class GetApiKeyRequest extends ItemRequest
{
    protected ?string $dto = ApiKey::class;

    /**
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $apiKeyId, Options|array $options = [])
    {
        $this->setOptions($options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/apikey/'.Path::segment($this->apiKeyId);
    }
}
