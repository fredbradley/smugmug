<?php

namespace FredBradley\SmugMug\Requests\Design;

use FredBradley\SmugMug\Data\Design\ProfileWidget;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ItemRequest;
use FredBradley\SmugMug\Support\Path;

/**
 * The published settings of a profile widget: GET /api/v2/widget/profile/{widgetId}/published.
 */
class GetPublishedProfileWidgetRequest extends ItemRequest
{
    protected ?string $dto = ProfileWidget::class;

    /**
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $widgetId, Options|array $options = [])
    {
        $this->setOptions($options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/widget/profile/'.Path::segment($this->widgetId).'/published';
    }
}
