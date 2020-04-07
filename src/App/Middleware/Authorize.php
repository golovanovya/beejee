<?php

namespace App\Middleware;

use League\Route\Http\Exception\ForbiddenException;
use League\Route\Http\Exception\UnauthorizedException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class Authorize implements MiddlewareInterface
{
    protected $allowed;
    protected $userAttribute = 'user';
    protected $loginRoute = '/login';
    
    /**
     * Allowed role
     * @param string $allowed allowed user role
     */
    public function __construct(string $allowed = '*')
    {
        $this->allowed = $allowed;
    }
    
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $user = $request->getAttribute($this->userAttribute);
        if ($user === null && $this->allowed !== '*') {
            return new \Laminas\Diactoros\Response\RedirectResponse($this->loginRoute);
//            throw new UnauthorizedException('Access denied!');
        }
        /* elseif ($user !== null && $this->allowed !== $user->getRole() && $this->allowed !== '*') {
            throw new ForbiddenException();
        } */
        return $handler->handle($request);
    }
}
