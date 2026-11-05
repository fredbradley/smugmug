<?php

use FredBradley\SmugMug\SmugMugService;
use Illuminate\Support\Facades\Artisan;

it('registers the service and merges the config', function () {
    expect(app('smugmug'))->toBeInstanceOf(SmugMugService::class)
        ->and(app('smugmug'))->toBe(app('smugmug'))
        ->and(config('smugmug.pagination.per_page'))->toBe(100)
        ->and(config('smugmug.callback_url'))->toBe('oob')
        ->and(config('smugmug.timeouts.upload'))->toBe(300);
});

it('has an install command that publishes the config', function () {
    expect(array_keys(Artisan::all()))->toContain('smugmug:install');
});
