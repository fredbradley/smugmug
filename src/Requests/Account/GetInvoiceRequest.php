<?php

namespace FredBradley\SmugMug\Requests\Account;

use FredBradley\SmugMug\Data\Account\Invoice;
use FredBradley\SmugMug\Query\Options;
use FredBradley\SmugMug\Requests\ItemRequest;
use FredBradley\SmugMug\Support\Path;

/**
 * An invoice, by key: GET /api/v2/invoice/{invoiceKey}.
 */
class GetInvoiceRequest extends ItemRequest
{
    protected ?string $dto = Invoice::class;

    /**
     * @param  Options|array<string, mixed>  $options
     */
    public function __construct(public readonly string $invoiceKey, Options|array $options = [])
    {
        $this->setOptions($options);
    }

    public function resolveEndpoint(): string
    {
        return '/api/v2/invoice/'.Path::segment($this->invoiceKey);
    }
}
