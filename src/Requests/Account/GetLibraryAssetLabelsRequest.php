<?php

namespace FredBradley\SmugMug\Requests\Account;

use FredBradley\SmugMug\Data\Account\Label;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ListRequest;
use FredBradley\SmugMug\Support\Path;

/**
 * The labels on a library asset: GET /api/v2/library/asset/{assetKey}!labels.
 */
class GetLibraryAssetLabelsRequest extends ListRequest
{
    protected ?string $dto = Label::class;

    /**
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $assetKey, Options|array $options = [])
    {
        $this->setOptions($options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/library/asset/'.Path::segment($this->assetKey).'!labels';
    }
}
