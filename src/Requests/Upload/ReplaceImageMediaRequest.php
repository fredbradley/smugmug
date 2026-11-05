<?php

namespace FredBradley\SmugMug\Requests\Upload;

use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\SmugMugRequest;
use FredBradley\SmugMug\Support\Path;
use FredBradley\SmugMug\Upload\Media;
use Saloon\Contracts\Body\HasBody;
use Saloon\Data\MultipartValue;
use Saloon\Enums\Method;
use Saloon\Traits\Body\HasMultipartBody;
use Saloon\Traits\Plugins\HasTimeout;

/**
 * Replace an image's file through the API, keeping its key, title, caption and comments, as multipart form data:
 * POST /api/v2/image/{imageKey}!replace. Media (required), ByteCount (required) and MD5Sum (required) come from
 * the Media.
 */
class ReplaceImageMediaRequest extends SmugMugRequest implements HasBody
{
    use HasMultipartBody;
    use HasTimeout;

    protected Method $method = Method::POST;

    protected string $responseShape = self::RESPONSE;

    protected float $requestTimeout;

    /**
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $imageKey, public readonly Media $media, Options|array $options = [])
    {
        $this->setOptions($options);
        $timeout = config('smugmug.timeouts.upload');
        $this->requestTimeout = is_numeric($timeout) && (float) $timeout > 0 ? (float) $timeout : 300.0;
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/image/'.Path::segment($this->imageKey).'!replace';
    }

    /**
     * @return list<MultipartValue>
     */
    protected function defaultBody(): array
    {
        return [
            new MultipartValue('ByteCount', (string) $this->media->size),
            new MultipartValue('MD5Sum', $this->media->md5),
            new MultipartValue('Media', $this->media->contents(), $this->media->fileName, ['Content-Type' => $this->media->mimeType]),
        ];
    }
}
