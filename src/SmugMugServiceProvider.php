<?php

namespace FredBradley\SmugMug;

use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class SmugMugServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('smugmug')
            ->hasConfigFile('smugmug')
            ->hasInstallCommand(function (InstallCommand $command) {
                $command->publishConfigFile();
            });

        $this->app->singleton('smugmug', fn ($app) => new SmugMugService);
    }
}
