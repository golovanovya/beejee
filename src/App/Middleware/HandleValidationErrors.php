<?php

namespace App\Middleware;

use App\SessionTrait;
use Laminas\Diactoros\Response\RedirectResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class HandleValidationErrors implements MiddlewareInterface
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
        $errors = $request->getAttribute($this->attribute);
        if (empty($errors)) {
            return $handler->handle($request);
        }
        $referer = $request->getHeaderLine('referer');
        $session = $this->extractSession($request, $this->sessionAttribute);
        
        $this->setFlash($session, $this->attribute, $errors, $this->segment);
        $this->setFlash($session, $this->oldData, $request->getParsedBody(), $this->segment);
        
        return new RedirectResponse(empty($referer) ? '/' : $referer);
    }
}
