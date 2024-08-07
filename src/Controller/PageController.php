<?php

declare(strict_types=1);

namespace App\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;

/**
 * Generic controller for static pages which render a template.
 */
class PageController
{
    public function __construct(
        private readonly Twig $view
    ) {
    }

    public function index(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->view->render(
            $response,
            'index.twig.html',
            []
        );
    }
}
