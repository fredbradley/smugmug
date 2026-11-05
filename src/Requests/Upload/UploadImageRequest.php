<?php

namespace FredBradley\SmugMug\Requests\Upload;

use FredBradley\SmugMug\Data\Upload\UploadResult;
use FredBradley\SmugMug\Requests\SmugMugRequest;
use FredBradley\SmugMug\SmugMugConnector;
use FredBradley\SmugMug\Support\Path;
use FredBradley\SmugMug\Support\Payload;
use FredBradley\SmugMug\Upload\Media;
use InvalidArgumentException;
use LogicException;
use Psr\Http\Message\StreamInterface;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\PendingRequest;
use Saloon\Http\Response;
use Saloon\Traits\Body\HasStreamBody;
use Saloon\Traits\Plugins\HasTimeout;

/**
 * Upload an image or video into an album, or replace an existing image, with SmugMug's uploader:
 * POST https://upload.smugmug.com/, the file as the body and its details in X-Smug-* headers. The uploader takes
 * OAuth only, so the connector needs an access token with Add (or Modify, to replace) permission.
 *
 * Details (the $details array, each sent as its X-Smug-* header):
 *  - Title, Caption: the image's title and caption
 *  - Keywords: a list, or one semicolon-separated string
 *  - Latitude, Longitude, Altitude: where it was taken
 *  - Hidden: true to hide it from visitors
 *  - FileName: the name to give it, instead of the media's
 *  - Pretty: true for an indented answer
 */
class UploadImageRequest extends SmugMugRequest implements HasBody
{
    use HasStreamBody;
    use HasTimeout;

    public const URL = 'https://upload.smugmug.com/';

    public const DETAILS = ['Title', 'Caption', 'Keywords', 'Latitude', 'Longitude', 'Altitude', 'Hidden', 'FileName', 'Pretty'];

    public ?bool $allowBaseUrlOverride = true;

    protected Method $method = Method::POST;

    protected string $responseShape = self::ONE;

    protected ?string $dto = UploadResult::class;

    protected float $requestTimeout;

    public readonly string $albumUri;

    /**
     * @param  string  $album  The album's key, or its URI (/api/v2/album/{key}).
     * @param  array<string, string|int|float|bool|list<string>>  $details
     * @param  string|null  $replaceImageUri  The image to replace (/api/v2/image/{key}), or null for a new one.
     */
    public function __construct(string $album, public readonly Media $media, public readonly array $details = [], public readonly ?string $replaceImageUri = null)
    {
        $this->albumUri = str_contains($album, '/') ? Path::uri($album) : '/api/v2/album/'.Path::segment($album);

        foreach (array_keys($details) as $name) {
            if (! in_array($name, self::DETAILS, true)) {
                throw new InvalidArgumentException("\"$name\" is not something the uploader takes; use one of ".implode(', ', self::DETAILS).'.');
            }
        }

        $timeout = config('smugmug.timeouts.upload');
        $this->requestTimeout = is_numeric($timeout) && (float) $timeout > 0 ? (float) $timeout : 300.0;
    }

    public function resolveEndpoint(): string
    {
        return self::URL;
    }

    public function isSafe(): bool
    {
        return false;
    }

    /**
     * The uploader refuses anything but OAuth, so an upload without an access token is stopped before it is sent.
     */
    public function boot(PendingRequest $pendingRequest): void
    {
        $connector = $pendingRequest->getConnector();

        if ($connector instanceof SmugMugConnector && ! $connector->hasToken()) {
            throw new LogicException('Uploading needs an access token: SmugMug\'s uploader takes OAuth only. Use SmugMug::withToken($token, $secret).');
        }
    }

    /**
     * @return array<string, string>
     */
    protected function defaultHeaders(): array
    {
        $headers = [
            'Content-Type' => $this->media->mimeType,
            'Content-Length' => (string) $this->media->size,
            'Content-MD5' => $this->media->contentMd5(),
            'X-Smug-AlbumUri' => $this->albumUri,
            'X-Smug-ResponseType' => 'JSON',
            'X-Smug-Version' => 'v2',
            'X-Smug-FileName' => $this->media->fileName,
        ];

        if ($this->replaceImageUri !== null) {
            $headers['X-Smug-ImageUri'] = Path::uri($this->replaceImageUri);
        }

        foreach ($this->details as $name => $value) {
            $headers['X-Smug-'.$name] = self::headerValue($value, $name === 'Keywords' ? '; ' : ',');
        }

        return $headers;
    }

    /**
     * @return resource|StreamInterface
     */
    protected function defaultBody(): mixed
    {
        return $this->media->contents();
    }

    protected function mapResponse(Response $response): ?UploadResult
    {
        $image = Payload::envelope($response)['Image'] ?? null;

        return is_array($image) ? UploadResult::from($image) : null;
    }

    /**
     * A header value: lists joined, booleans as true and false, and no line breaks (which would start a header of
     * their own).
     *
     * @param  string|int|float|bool|list<string>  $value
     */
    private static function headerValue(mixed $value, string $glue): string
    {
        $value = match (true) {
            is_bool($value) => $value ? 'true' : 'false',
            is_array($value) => implode($glue, array_map('strval', $value)),
            default => (string) $value,
        };

        return trim((string) preg_replace('/[\r\n]+/', ' ', $value));
    }
}
