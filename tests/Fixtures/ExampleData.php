<?php

namespace FredBradley\SmugMug\Tests\Fixtures;

use Carbon\CarbonImmutable;
use FredBradley\SmugMug\Data\Data;
use Illuminate\Support\Collection;

readonly class ExampleData extends Data
{
    protected const ALIASES = ['ImageUrl4K' => '4KImageUrl'];

    protected const HIDDEN = ['Password'];

    protected const LISTS = ['Children' => ExampleData::class];

    /**
     * @param  list<mixed>|null  $Keywords
     * @param  Collection<int, ExampleData>|null  $Children
     * @param  array<string, mixed>  $Uris
     * @param  array<string, mixed>  $extra
     */
    public function __construct(
        public ?string $Name = null,
        public ?int $Count = null,
        public ?float $Ratio = null,
        public ?bool $Hidden = null,
        public ?CarbonImmutable $LastUpdated = null,
        public ?array $Keywords = null,
        public ?string $Password = null,
        public ?string $ImageUrl4K = null,
        public ?Collection $Children = null,
        public ?ExampleData $Parent = null,
        public ?string $Uri = null,
        public array $Uris = [],
        public array $extra = [],
    ) {}
}
