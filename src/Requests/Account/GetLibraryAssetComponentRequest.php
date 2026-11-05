<?php

namespace FredBradley\SmugMug\Requests\Account;

use FredBradley\SmugMug\Data\Account\Component;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ItemRequest;
use FredBradley\SmugMug\Support\Path;

/**
 * One file of a library asset: GET /api/v2/library/asset/{assetKey}/component/{componentType}/{componentKey}.
 */
class GetLibraryAssetComponentRequest extends ItemRequest
{
    protected ?string $dto = Component::class;

    /**
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $assetKey, public readonly string $componentType, public readonly string $componentKey, Options|array $options = [])
    {
        $this->setOptions($options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/library/asset/'.Path::segment($this->assetKey).'/component/'.Path::segment($this->componentType).'/'.Path::segment($this->componentKey);
    }
}
