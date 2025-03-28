<?php

declare(strict_types=1);

namespace App\Http;

use App\Logs\LogAccessor;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class IndexLogRequestsAction
{
    public function __construct(private readonly LogAccessor $logAccessor) {}

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function __invoke(Request $request, Response $response): Response
    {
        $requests = $this->logAccessor->getRequests();

        $view = Twig::fromRequest($request);

        return $view->render(
            $response,
            'index-requests.html.twig',
            [
                'requests' => $requests,
            ]
        );
    }
}
