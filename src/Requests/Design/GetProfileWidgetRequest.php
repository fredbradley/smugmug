<?php

namespace FredBradley\SmugMug\Requests\Design;

use FredBradley\SmugMug\Data\Design\Widget;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ItemRequest;
use FredBradley\SmugMug\Support\Path;

/**
 * A profile widget, by id: GET /api/v2/widget/profile/{widgetId}.
 */
class GetProfileWidgetRequest extends ItemRequest
{
    protected ?string $dto = Widget::class;

    /**
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $widgetId, Options|array $options = [])
    {
        $this->setOptions($options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/widget/profile/'.Path::segment($this->widgetId);
    }
}
