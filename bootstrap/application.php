<?php

declare(strict_types=1);

use App\Env;
use DI\Bridge\Slim\Bridge;

return (function () {
    $dotenv = Dotenv\Dotenv::create(Env::getRepository(), dirname(__DIR__));
    $dotenv->safeLoad();

    $container = require 'container.php';

    $app = Bridge::create($container);

    (require 'config/middleware.php')($app);
    (require 'config/routes.php')($app);

    return $app;
})();
