<?php

namespace FredBradley\SmugMug\Requests\Theme;

use FredBradley\SmugMug\Data\Social\Theme;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ItemRequest;
use FredBradley\SmugMug\Support\Path;

/**
 * An album theme, by id: GET /api/v2/theme/{themeId}.
 */
class GetThemeRequest extends ItemRequest
{
    protected ?string $dto = Theme::class;

    /**
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $themeId, Options|array $options = [])
    {
        $this->setOptions($options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/theme/'.Path::segment($this->themeId);
    }
}
