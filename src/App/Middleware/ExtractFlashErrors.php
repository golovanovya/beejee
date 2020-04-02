<?php

namespace App\Middleware;

use App\SessionTrait;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ExtractFlashErrors implements MiddlewareInterface
{
    use SessionTrait;
    
    private $attribute = 'errors';
    private $oldData = 'oldData';
    private $sessionAttribute = 'session';
    private $segment;
    
    public function __construct(string $segment = '')
    {
        $this->segment = $segment;
    }
    
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $session = $this->extractSession($request, $this->sessionAttribute);
        
        $errors = $this->getFlash($session, $this->attribute, $this->segment);
        $oldData = $this->getFlash($session, $this->oldData, $this->segment);
        
        $request = $request->withAttribute($this->attribute, $errors)
            ->withAttribute($this->oldData, $oldData);
        
        return $handler->handle($request);
    }
}
