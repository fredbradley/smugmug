<?php

namespace FredBradley\SmugMug\Requests\Design;

use FredBradley\SmugMug\Data\Design\ProfileWidget;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\WriteRequest;
use FredBradley\SmugMug\Support\Path;
use JsonSerializable;
use Saloon\Enums\Method;

/**
 * Change the working settings of a profile widget: PATCH /api/v2/widget/profile/{widgetId}/working.
 *
 * Parameters (in the body):
 *  - MarginTop, MarginRight, MarginBottom, MarginLeft
 *  - IconColor: default, primary, accent or official
 *  - IconType: default, square or circle
 *  - NewTab, ShowCoverPhoto, ShowEmail, ShowIcons, ShowProfilePhoto
 */
class UpdateWorkingProfileWidgetRequest extends WriteRequest
{
    protected Method $method = Method::PATCH;

    protected ?string $dto = ProfileWidget::class;

    /**
     * @param  array<mixed>|JsonSerializable  $body  SmugMug's field names and values, or a model.
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $widgetId, array|JsonSerializable $body = [], Options|array $options = [])
    {
        $this->setBody($body, $options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/widget/profile/'.Path::segment($this->widgetId).'/working';
    }
}
