<?php

declare(strict_types=1);

namespace App;

use Closure;
use Dotenv\Repository\Adapter\PutenvAdapter;
use Dotenv\Repository\RepositoryBuilder;
use Dotenv\Repository\RepositoryInterface;
use PhpOption\Option;
use PhpOption\Some;
use RuntimeException;

class Env
{
    /**
     * Indicates if the putenv adapter is enabled.
     */
    protected static bool $putEnv = true;

    /**
     * The environment repository instance.
     *
     * @var RepositoryInterface|null
     */
    protected static ?RepositoryInterface $repository;

    /**
     * The list of custom adapters for loading environment variables.
     *
     * @var array<Closure>
     */
    protected static array $customAdapters = [];

    /**
     * Enable the putenv adapter.
     */
    public static function enablePutenv(): void
    {
        static::$putEnv = true;
        static::$repository = null;
    }

    /**
     * Disable the putenv adapter.
     */
    public static function disablePutenv(): void
    {
        static::$putEnv = false;
        static::$repository = null;
    }

    /**
     * Register a custom adapter creator Closure.
     */
    public static function extend(Closure $callback, ?string $name = null): void
    {
        if (!is_null($name)) {
            static::$customAdapters[$name] = $callback;
        } else {
            static::$customAdapters[] = $callback;
        }
    }

    /**
     * Get the environment repository instance.
     */
    public static function getRepository(): RepositoryInterface
    {
        if (!isset(static::$repository)) {
            $builder = RepositoryBuilder::createWithDefaultAdapters();

            if (static::$putEnv) {
                $builder = $builder->addAdapter(PutenvAdapter::class);
            }

            foreach (static::$customAdapters as $adapter) {
                $builder = $builder->addAdapter($adapter());
            }

            static::$repository = $builder->immutable()->make();
        }

        return static::$repository;
    }

    /**
     * Get the value of an environment variable.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        return self::getOption($key)->getOrCall(fn() => is_callable($default) ? $default() : $default);
    }

    /**
     * Get the value of a required environment variable.
     *
     * @throws RuntimeException
     */
    public static function getOrFail(string $key): mixed
    {
        return self::getOption($key)->getOrThrow(new RuntimeException("Environment variable [$key] has no value."));
    }

    /**
     * Get the possible option for this environment variable.
     */
    protected static function getOption(string $key): Option|Some
    {
        return Option::fromValue(static::getRepository()->get($key))
            ->map(function ($value) {
                switch (strtolower($value)) {
                    case 'true':
                    case '(true)':
                        return true;
                    case 'false':
                    case '(false)':
                        return false;
                    case 'empty':
                    case '(empty)':
                        return '';
                    case 'null':
                    case '(null)':
                        return null;
                }

                if (preg_match('/\A([\'"])(.*)\1\z/', $value, $matches)) {
                    return $matches[2];
                }

                return $value;
            });
    }
}
