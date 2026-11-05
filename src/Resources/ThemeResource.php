<?php

namespace FredBradley\SmugMug\Resources;

use FredBradley\SmugMug\Data\Social\Theme;
use FredBradley\SmugMug\Listing;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\Theme\GetThemeRequest;
use FredBradley\SmugMug\Requests\Theme\GetThemesRequest;

/**
 * Album themes (legacy SmugMug), from SmugMug::themes().
 */
class ThemeResource extends BaseResource
{
    /**
     * Every album theme (legacy SmugMug): GET /api/v2/theme!themes.
     *
     * @param  Options|array<string, mixed>  $options
     * @return Listing<Theme>
     */
    public function all(Options|array $options = []): Listing
    {
        return $this->listing(fn (Options $o) => new GetThemesRequest($o), $options);
    }

    /**
     * An album theme, by id: GET /api/v2/theme/{themeId}.
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function get(string $themeId, Options|array $options = []): ?Theme
    {
        return $this->fetch(new GetThemeRequest($themeId, $this->options($options)));
    }
}
