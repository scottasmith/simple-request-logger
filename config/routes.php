<?php

declare(strict_types=1);

use App\Http\HomeAction;
use App\Http\IndexLogRequestsAction;
use App\Http\LogRequestAction;
use App\Http\ShowRequestAction;
use Slim\App;

return function (App $app) {
    $app->get('/', HomeAction::class);

    $app->get('/log-request', LogRequestAction::class);

    $app->get('/protected/index', IndexLogRequestsAction::class);

    $app->get('/protected/show-request/{filename}', ShowRequestAction::class);
};
