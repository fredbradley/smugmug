<?php

namespace FredBradley\SmugMug\Requests;

/**
 * A POST (or DELETE) that does something rather than create an object: move or sort images, rotate or crop them,
 * add keywords, apply a watermark. It carries its parameters as JSON, and answers with the envelope's Response
 * object (which may hold the URI of an asynchronous task's status).
 */
abstract class ActionRequest extends WriteRequest
{
    protected string $responseShape = self::RESPONSE;

    /**
     * The parameters SmugMug takes as one comma-separated string of URIs, which may be given as a list.
     *
     * @var list<string>
     */
    protected array $commaSeparated = [];

    /**
     * @return array<mixed>
     */
    protected function defaultBody(): array
    {
        $body = parent::defaultBody();

        foreach ($this->commaSeparated as $name) {
            if (isset($body[$name]) && is_array($body[$name])) {
                $body[$name] = implode(',', $body[$name]);
            }
        }

        return $body;
    }
}
