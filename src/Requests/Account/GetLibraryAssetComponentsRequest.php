<?php

namespace FredBradley\SmugMug\Requests\Account;

use FredBradley\SmugMug\Data\Account\Component;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ListRequest;
use FredBradley\SmugMug\Support\Path;

/**
 * The files of a library asset: GET /api/v2/library/asset/{assetKey}!components.
 */
class GetLibraryAssetComponentsRequest extends ListRequest
{
    protected ?string $dto = Component::class;

    /**
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $assetKey, Options|array $options = [])
    {
        $this->setOptions($options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/library/asset/'.Path::segment($this->assetKey).'!components';
    }
}
