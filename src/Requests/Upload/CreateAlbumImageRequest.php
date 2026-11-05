<?php

namespace FredBradley\SmugMug\Requests\Upload;

use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\SmugMugRequest;
use FredBradley\SmugMug\Support\Path;
use FredBradley\SmugMug\Upload\Media;
use InvalidArgumentException;
use Saloon\Contracts\Body\HasBody;
use Saloon\Data\MultipartValue;
use Saloon\Enums\Method;
use Saloon\Traits\Body\HasMultipartBody;
use Saloon\Traits\Plugins\HasTimeout;

/**
 * Add an image to an album through the API rather than the uploader, as multipart form data:
 * POST /api/v2/album/{albumKey}!images.
 *
 * Fields (the $fields array):
 *  - Title, Caption: the image's title and caption
 *  - Keywords: a list
 *  - Hidden: true to hide it from visitors
 *  - ReplaceMediaUri: the image to replace with this file
 *
 * Media (required), ByteCount (required) and MD5Sum (required) come from the Media.
 */
class CreateAlbumImageRequest extends SmugMugRequest implements HasBody
{
    use HasMultipartBody;
    use HasTimeout;

    public const FIELDS = ['Title', 'Caption', 'Keywords', 'Hidden', 'ReplaceMediaUri'];

    protected Method $method = Method::POST;

    protected string $responseShape = self::AUTO;

    protected float $requestTimeout;

    /**
     * @param  array<string, string|bool|list<string>>  $fields
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $albumKey, public readonly Media $media, public readonly array $fields = [], Options|array $options = [])
    {
        foreach (array_keys($fields) as $name) {
            if (! in_array($name, self::FIELDS, true)) {
                throw new InvalidArgumentException("\"$name\" is not a field of an album image upload; use one of ".implode(', ', self::FIELDS).'.');
            }
        }

        $this->setOptions($options);
        $timeout = config('smugmug.timeouts.upload');
        $this->requestTimeout = is_numeric($timeout) && (float) $timeout > 0 ? (float) $timeout : 300.0;
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/album/'.Path::segment($this->albumKey).'!images';
    }

    /**
     * @return list<MultipartValue>
     */
    protected function defaultBody(): array
    {
        $parts = [
            new MultipartValue('ByteCount', (string) $this->media->size),
            new MultipartValue('MD5Sum', $this->media->md5),
        ];

        foreach ($this->fields as $name => $value) {
            $value = match (true) {
                is_bool($value) => $value ? 'true' : 'false',
                is_array($value) => implode('; ', $value),
                default => (string) $value,
            };
            $parts[] = new MultipartValue($name, $value);
        }

        $parts[] = new MultipartValue('Media', $this->media->contents(), $this->media->fileName, ['Content-Type' => $this->media->mimeType]);

        return $parts;
    }
}
