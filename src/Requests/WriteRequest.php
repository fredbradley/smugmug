<?php

namespace FredBradley\SmugMug\Requests;

use FredBradley\SmugMug\Data\Data;
use FredBradley\SmugMug\Query\Options;
use JsonSerializable;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Traits\Body\HasJsonBody;

/**
 * A POST (create) or PATCH (change) carrying an object's fields as JSON: an array of SmugMug's field names, or a
 * model. A PATCH changes only the fields it carries. SmugMug answers with the object as stored.
 */
abstract class WriteRequest extends SmugMugRequest implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    protected string $responseShape = self::ONE;

    /** @var array<mixed>|JsonSerializable */
    protected array|JsonSerializable $fields = [];

    /**
     * @param  array<mixed>|JsonSerializable  $body
     * @param  Options|array<string, mixed>  $options
     */
    protected function setBody(array|JsonSerializable $body, Options|array $options): void
    {
        $this->fields = $body;
        $this->setOptions($options);
    }

    /**
     * The fields SmugMug sends with an object but never takes back, which a model's toArray() carries.
     */
    public const READ_ONLY = ['Uri', 'Uris', 'WebUri', 'UriDescription', 'ResponseLevel'];

    /**
     * The JSON body: the fields as given, or a model's fields without the ones SmugMug never takes back.
     *
     * @return array<mixed>
     */
    protected function defaultBody(): array
    {
        if ($this->fields instanceof Data) {
            return array_diff_key($this->fields->toArray(), array_flip(self::READ_ONLY));
        }

        return $this->fields instanceof JsonSerializable ? (array) $this->fields->jsonSerialize() : $this->fields;
    }
}
