<?php

namespace App\Middleware;

use Laminas\Diactoros\Response\RedirectResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class Anon implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $session = $request->getAttribute('session');
        $segment = $session->getSegment('jobController');
        if ($segment->get('isAdmin')) {
            $segment->setFlash('failMessage', 'Операция доступна только неавторизованным пользователям.');
            return new RedirectResponse('/');
        }
        return $handler->handle($request);
    }
}
