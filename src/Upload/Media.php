<?php

namespace FredBradley\SmugMug\Upload;

use InvalidArgumentException;
use Psr\Http\Message\StreamInterface;

/**
 * A file to upload: its contents (streamed from disk, not read into memory), name, size, MD5 and type. SmugMug checks
 * the MD5 against what it receives, so a truncated upload fails rather than leaving a broken image.
 */
final class Media
{
    /** @var resource|StreamInterface */
    private mixed $contents;

    /**
     * @param  resource|StreamInterface  $contents
     */
    public function __construct(
        mixed $contents,
        public readonly string $fileName,
        public readonly string $md5,
        public readonly int $size,
        public readonly string $mimeType = 'application/octet-stream',
    ) {
        if (! is_resource($contents) && ! $contents instanceof StreamInterface) {
            throw new InvalidArgumentException('Media contents must be a stream or a resource.');
        }

        if (! preg_match('/^[a-f0-9]{32}$/', $md5)) {
            throw new InvalidArgumentException('The MD5 must be 32 hexadecimal characters.');
        }

        $this->contents = $contents;
    }

    /**
     * A file on disk, streamed when it is sent.
     */
    public static function fromPath(string $path, ?string $fileName = null, ?string $mimeType = null): self
    {
        if (! is_file($path) || ! is_readable($path)) {
            throw new InvalidArgumentException("\"$path\" is not a readable file.");
        }

        $handle = fopen($path, 'rb');

        if ($handle === false) {
            throw new InvalidArgumentException("\"$path\" could not be opened.");
        }

        return new self(
            $handle,
            $fileName ?? basename($path),
            (string) md5_file($path),
            (int) filesize($path),
            $mimeType ?? self::mimeTypeOf($path),
        );
    }

    /**
     * Contents already in memory, such as an uploaded file's or a generated image's.
     */
    public static function fromContents(string $contents, string $fileName, ?string $mimeType = null): self
    {
        $handle = fopen('php://temp', 'r+b');

        if ($handle === false) {
            throw new InvalidArgumentException('A temporary stream could not be opened.');
        }

        fwrite($handle, $contents);
        rewind($handle);

        return new self($handle, $fileName, md5($contents), strlen($contents), $mimeType ?? self::mimeTypeFromName($fileName));
    }

    /**
     * The contents, rewound to the start.
     *
     * @return resource|StreamInterface
     */
    public function contents(): mixed
    {
        if ($this->contents instanceof StreamInterface) {
            if ($this->contents->isSeekable()) {
                $this->contents->rewind();
            }
        } else {
            rewind($this->contents);
        }

        return $this->contents;
    }

    /**
     * The MD5 as the Content-MD5 header carries it: the raw digest, base64-encoded.
     */
    public function contentMd5(): string
    {
        return base64_encode((string) hex2bin($this->md5));
    }

    private static function mimeTypeOf(string $path): string
    {
        $type = function_exists('mime_content_type') ? @mime_content_type($path) : false;

        return is_string($type) && $type !== '' && $type !== 'application/octet-stream' ? $type : self::mimeTypeFromName($path);
    }

    private static function mimeTypeFromName(string $name): string
    {
        return match (strtolower(pathinfo($name, PATHINFO_EXTENSION))) {
            'jpg', 'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'heic' => 'image/heic',
            'heif' => 'image/heif',
            'webp' => 'image/webp',
            'tif', 'tiff' => 'image/tiff',
            'mp4' => 'video/mp4',
            'mov' => 'video/quicktime',
            'm4v' => 'video/x-m4v',
            'avi' => 'video/x-msvideo',
            'mts', 'm2ts' => 'video/mp2t',
            default => 'application/octet-stream',
        };
    }
}
