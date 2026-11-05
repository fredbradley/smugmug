<?php

namespace FredBradley\SmugMug;

use BadMethodCallException;
use InvalidArgumentException;

/**
 * What the SmugMug facade resolves: the default connector, built from config('smugmug') the first time it is
 * needed, and any named connections.
 *
 * @mixin SmugMugConnector
 */
class SmugMugService
{
    protected SmugMugConnector $connector;

    /** @var array<string, SmugMugConnector> */
    protected array $connections = [];

    /**
     * The connector for another application or account, configured under smugmug.connections.{name}.
     */
    public function connection(string $name): SmugMugConnector
    {
        $config = config("smugmug.connections.$name");

        if (! is_array($config)) {
            throw new InvalidArgumentException('No SmugMug connection named "'.$name.'" is configured in smugmug.connections.');
        }

        return $this->connections[$name] ??= self::connect($config, 'The SmugMug connection "'.$name.'"');
    }

    /**
     * The default connector, as configured in config/smugmug.php.
     */
    public function connector(): SmugMugConnector
    {
        return $this->connector ??= self::connect((array) config('smugmug'), 'The default SmugMug connection');
    }

    /**
     * @param  array<mixed>  $parameters
     */
    public function __call(string $method, array $parameters): mixed
    {
        $connector = $this->connector();

        if (method_exists($connector, $method)) {
            return $connector->{$method}(...$parameters);
        }

        throw new BadMethodCallException("Method {$method} does not exist.");
    }

    /**
     * @param  array<string, mixed>  $config
     */
    private static function connect(#[\SensitiveParameter] array $config, string $connection): SmugMugConnector
    {
        if (! is_string($config['api_key'] ?? null) || $config['api_key'] === '') {
            throw new InvalidArgumentException("$connection needs an api_key (SMUGMUG_API_KEY).");
        }

        $value = fn (string $setting) => is_string($config[$setting] ?? null) && $config[$setting] !== '' ? $config[$setting] : null;

        if ($value('access_token') !== null && ($value('api_secret') === null || $value('access_token_secret') === null)) {
            throw new InvalidArgumentException("$connection has an access_token, so it needs the api_secret and the access_token_secret too.");
        }

        return new SmugMugConnector($config['api_key'], $value('api_secret'), $value('access_token'), $value('access_token_secret'));
    }
}
