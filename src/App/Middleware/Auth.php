<?php

namespace App\Middleware;

use Laminas\Diactoros\Response\RedirectResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class Auth implements MiddlewareInterface
{
    /**
     * @var string allowed role
     */
    private $allowed;
    
    private $authManager;
    
    /**
     * @param string $allowed allowed role
     */
    public function __construct(\App\AuthManager $authManager, $allowed = '*')
    {
        $this->authManager = $authManager;
        $this->allowed = $allowed;
    }
    
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $user = $this->authManager->getUser();
        if (!$user && $this->allowed !== '*' || $this->allowed !== $user->getRole() && $this->allowed !== '*') {
            $request
                ->getAttribute('session')
                ->getSegment('jobController')
                ->setFlash('failMessage', 'Операция доступна только администратору.');
            return new RedirectResponse('/');
        }
        return $handler->handle($request);
    }
}
