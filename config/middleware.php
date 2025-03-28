<?php

declare(strict_types=1);

use Slim\App;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;

return function (App $app) {
    $app->addErrorMiddleware(false, true, true);

    $twig = Twig::create(__DIR__ . '/../templates', ['cache' => false]);
    $app->add(TwigMiddleware::create($app, $twig));

    $container = $app->getContainer();
    $config = $container->get('config');

    if (!isset($config['auth']['user']) || !isset($config['auth']['pass'])) {
        throw new Exception('Credentials missing');
    }

    $app->add(new Tuupola\Middleware\HttpBasicAuthentication([
        'path' => '/protected',
        'secure' => false,
        'users'=> [
            $config['auth']['user'] => $config['auth']['pass'],
        ]
    ]));
};
