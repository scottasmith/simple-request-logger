<?php

declare(strict_types=1);

namespace App\Http;

use App\Logs\LogAccessor;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class LogRequestAction
{
    public function __construct(private readonly LogAccessor $logAccessor) {}

    public function __invoke(Request $request, Response $response): Response
    {
        $params = $request->getQueryParams();

        $uuid = $this->logAccessor->storeLog($params);

        $response->getBody()->write('Stored. File -  ' . $uuid);

        return $response;
    }
}
