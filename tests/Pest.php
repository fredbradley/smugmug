<?php

use FredBradley\SmugMug\Tests\TestCase;
use Saloon\Config;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| Every test boots the package in Testbench with an array cache. No request may reach the real SmugMug: anything
| sent without a mock client throws a StrayRequestException. Fake SmugMug with Tests\Helpers\FakeSmugMug.
|
*/

Config::preventStrayRequests();

pest()->extend(TestCase::class)->in('Feature', 'Spec');
