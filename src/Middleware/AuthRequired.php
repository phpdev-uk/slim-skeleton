<?php

declare(strict_types=1);

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Response;
use Slim\Routing\RouteContext;

class AuthRequired implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        /** @todo Replace this with your authentication 'is logged in' check */
        if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']) {
            /** @todo Replace this with whatever attributes you want to pass into the request, such as the user ID */
            $request = $request->withAttribute('user_id', $_SESSION['user_id']);

            $response = $handler->handle($request);
            return $response;
        }

        $routeParser = RouteContext::fromRequest($request)->getRouteParser();

        $response = new Response();
        /** @todo Replace this with a redirect to your login route */
        return $response->withStatus(302)->withHeader('Location', $routeParser->urlFor('login'));
    }
}
