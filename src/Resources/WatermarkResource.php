<?php

namespace FredBradley\SmugMug\Resources;

use FredBradley\SmugMug\Data\Data;
use FredBradley\SmugMug\Data\Social\Watermark;
use FredBradley\SmugMug\Data\Social\WatermarkMetadata;
use FredBradley\SmugMug\Listing;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\Watermark\CreateWatermarkRequest;
use FredBradley\SmugMug\Requests\Watermark\DeleteWatermarkRequest;
use FredBradley\SmugMug\Requests\Watermark\GetUserWatermarksRequest;
use FredBradley\SmugMug\Requests\Watermark\GetWatermarkMetadataRequest;
use FredBradley\SmugMug\Requests\Watermark\GetWatermarkRequest;
use FredBradley\SmugMug\Requests\Watermark\UpdateWatermarkMetadataRequest;
use FredBradley\SmugMug\Requests\Watermark\UpdateWatermarkRequest;

/**
 * Watermarks, from SmugMug::watermarks().
 */
class WatermarkResource extends BaseResource
{
    /**
     * A user's watermarks: GET /api/v2/user/{nickname}!watermarks.
     *
     *  - Unavailable: include the watermarks the plan no longer allows
     *
     * @param  Options|array<string, mixed>  $options
     * @return Listing<Watermark>
     */
    public function forUser(string $nickname, Options|array $options = []): Listing
    {
        return $this->listing(fn (Options $o) => new GetUserWatermarksRequest($nickname, $o), $options);
    }

    /**
     * Create a watermark: POST /api/v2/watermark.
     *
     *  - Name (required)
     *  - ImageID (required): the numeric id of the image to use
     *  - ImageUri: or its URI
     *  - Dissolve: opacity, 0 to 100
     *  - Pinned: Center, TopLeft, TopRight, BottomLeft, BottomRight, Tile, Top, Left, Right or Bottom
     *  - SmallestSize: Th, S, M, L or XL, the smallest size watermarked
     *
     * @param  array<string, mixed>|Data  $fields
     * @param  Options|array<string, mixed>  $options
     */
    public function create(array|Data $fields, Options|array $options = []): ?Watermark
    {
        return $this->fetch(new CreateWatermarkRequest($fields, $this->options($options)));
    }

    /**
     * A watermark, by id: GET /api/v2/watermark/{watermarkId}.
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function get(string $watermarkId, Options|array $options = []): ?Watermark
    {
        return $this->fetch(new GetWatermarkRequest($watermarkId, $this->options($options)));
    }

    /**
     * Change a watermark: PATCH /api/v2/watermark/{watermarkId}.
     *
     *  - Name, ImageID, ImageKey, Dissolve, Pinned, SmallestSize
     *
     * @param  array<string, mixed>|Data  $fields
     * @param  Options|array<string, mixed>  $options
     */
    public function update(string $watermarkId, array|Data $fields, Options|array $options = []): ?Watermark
    {
        return $this->fetch(new UpdateWatermarkRequest($watermarkId, $fields, $this->options($options)));
    }

    /**
     * Delete a watermark: DELETE /api/v2/watermark/{watermarkId}.
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function delete(string $watermarkId, Options|array $options = []): bool
    {
        return $this->perform(new DeleteWatermarkRequest($watermarkId, $this->options($options)));
    }

    /**
     * The text of a text watermark: GET /api/v2/watermark/{watermarkId}!metadata.
     *
     * @param  Options|array<string, mixed>  $options
     */
    public function metadata(string $watermarkId, Options|array $options = []): ?WatermarkMetadata
    {
        return $this->fetch(new GetWatermarkMetadataRequest($watermarkId, $this->options($options)));
    }

    /**
     * Change the text of a text watermark: PATCH /api/v2/watermark/{watermarkId}!metadata.
     *
     *  - TextContents, TextColor, TextBackgroundColor, TextFont, TextSize
     *
     * @param  array<string, mixed>|Data  $fields
     * @param  Options|array<string, mixed>  $options
     */
    public function updateMetadata(string $watermarkId, array|Data $fields, Options|array $options = []): ?WatermarkMetadata
    {
        return $this->fetch(new UpdateWatermarkMetadataRequest($watermarkId, $fields, $this->options($options)));
    }
}
