<?php

use Carbon\CarbonImmutable;
use FredBradley\SmugMug\Data\Data;
use FredBradley\SmugMug\Data\Models;

$models = require __DIR__.'/models.php';

it('builds every model from SmugMug\'s values and serialises it back', function (string $class, array $locators) {
    $attributes = [];
    $expected = [];
    $reflection = new ReflectionClass($class);
    $aliases = $reflection->getConstant('ALIASES') ?: [];
    $hidden = $reflection->getConstant('HIDDEN') ?: [];

    expect($reflection->isSubclassOf(Data::class))->toBeTrue();

    foreach ($reflection->getConstructor()->getParameters() as $parameter) {
        $type = $parameter->getType();
        $name = $parameter->getName();
        $key = $aliases[$name] ?? $name;
        expect($type)->toBeInstanceOf(ReflectionNamedType::class)
            ->and($parameter->isDefaultValueAvailable())->toBeTrue()
            ->and($name)->toMatch('/^[A-Za-z_]\w*$/');

        if ($name === 'extra') {
            continue;
        }

        [$attributes[$key], $expected[$key]] = match ($type->getName()) {
            'int' => ['12', 12],
            'float' => ['1.5', 1.5],
            'bool' => [true, true],
            'string' => [7, '7'],
            'array' => [['a' => 1], ['a' => 1]],
            'mixed' => ['x', 'x'],
            CarbonImmutable::class => ['2026-01-01T00:00:00+00:00', '2026-01-01T00:00:00+00:00'],
        };

        if (in_array($name, $hidden, true)) {
            unset($expected[$key]);
        }
    }

    $model = $class::from([...$attributes, 'SomethingNew' => 'kept']);

    expect($model->toArray())->toEqual([...$expected, 'SomethingNew' => 'kept'])
        ->and($model->extra)->toBe(['SomethingNew' => 'kept'])
        ->and(json_encode($model))->toBe(json_encode($model->toArray()));
})->with($models);

it('maps every Locator to its model', function () use ($models) {
    foreach ($models as [$class, $locators]) {
        foreach ($locators as $locator) {
            expect(Models::for($locator))->toBe($class);
        }
    }

    expect(Models::for('SomethingElse'))->toBeNull()
        ->and(Models::for(null))->toBeNull()
        ->and(collect(Models::LOCATORS)->unique()->sort()->values()->all())->toBe(collect($models)->map(fn (array $row) => $row[0])->sort()->values()->all());
});

it('has a model for every file under Data', function () use ($models) {
    $files = collect(glob(__DIR__.'/../../src/Data/*/*.php'))
        ->map(fn (string $file) => basename($file, '.php'))
        ->reject(fn (string $name) => $name === 'UploadResult')
        ->sort()->values();

    expect($files->all())->toBe(collect($models)->keys()->sort()->values()->all());
});
