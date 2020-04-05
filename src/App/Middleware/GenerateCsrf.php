<?php

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class GenerateCsrf implements MiddlewareInterface
{
    use \App\SessionTrait;
    
    private $attribute;
    
    public function __construct(string $attribute = 'csrf')
    {
        $this->attribute = $attribute;
    }
    
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $session = $this->extractSession($request);
        return $handler->handle($request->withAttribute($this->attribute, $session->getCsrfToken()->getValue()));
    }
}
