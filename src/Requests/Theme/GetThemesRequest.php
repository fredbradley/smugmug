<?php

namespace FredBradley\SmugMug\Requests\Theme;

use FredBradley\SmugMug\Data\Social\Theme;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ListRequest;

/**
 * Every album theme (legacy SmugMug): GET /api/v2/theme!themes.
 */
class GetThemesRequest extends ListRequest
{
    protected ?string $dto = Theme::class;

    /**
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(Options|array $options = [])
    {
        $this->setOptions($options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/theme!themes';
    }
}
