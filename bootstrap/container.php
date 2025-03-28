<?php

declare(strict_types=1);

use App\Env;
use App\Http\ShowRequestAction;
use App\Logs\LogAccessor as LogAccessorAlias;
use DI\Container;
use Psr\Container\ContainerInterface;

return new Container(
    [
        'config' => [
            'storage' => [
                'logDirectory' => dirname(__DIR__) . '/storage',
            ],

            'shaSign' => [
                'passphrase' => Env::get('SHASIGN_PASSPHRASE', 'test'),
            ],

            'auth' => [
                'user' => Env::get('AUTH_USER'),
                'pass' => Env::get('AUTH_PASS'),
            ]
        ],

        LogAccessorAlias::class => DI\factory(function (ContainerInterface $container) {
            $config = $container->get('config');

            if (!isset($config['storage']['logDirectory'])) {
                throw new Exception('logDirectory config not found!');
            }

            return new LogAccessorAlias(
                $config['storage']['logDirectory'],
            );
        }),

        ShowRequestAction::class => DI\factory(function (ContainerInterface $container) {
            $config = $container->get('config');

            if (!isset($config['shaSign']['passphrase'])) {
                throw new Exception('shaSign[passphrase] config not found!');
            }

            return new ShowRequestAction(
                $container->get(LogAccessorAlias::class),
                $config['shaSign']['passphrase'],
            );
        }),
    ],
);
