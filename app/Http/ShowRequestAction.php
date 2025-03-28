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

class ShowRequestAction
{
    public function __construct(
        private readonly LogAccessor $logAccessor,
        private readonly string $passphrase,
    ) {}

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function __invoke(Request $request, Response $response, string $filename): Response
    {
        $details = $this->logAccessor->getRequest($filename);
        if (null === $details) {
            return $response->withStatus(404);
        }

        $view = Twig::fromRequest($request);

        return $view->render(
            $response,
            'show-request.html.twig',
            [
                'details' => $details,
                'shaSign' => $this->getShaSign($details['contents']) ?? 'N/A',
            ]
        );
    }

    private function getShaSign(array $params): ?string
    {
        if (!isset($params['SHASIGN'])) {
            return null;
        }
        unset($params['SHASIGN']);

        ksort($params);

        $concatParams = '';
        foreach ($params as $paramKey => $paramValue) {
            $concatParams .= sprintf('%s=%s%s', $paramKey, $paramValue, $this->passphrase);
        }

        return strtoupper(hash('sha512', $concatParams));
    }
}
