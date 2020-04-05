<?php

namespace App\Middleware;

use App\SessionTrait;
use Laminas\Diactoros\Response\RedirectResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ValidateCsrf implements MiddlewareInterface
{
    use SessionTrait;
    
    private $attribute;
    private $csrfAttribute;
    private $sessionAttribute = 'session';
    private $segment;
    private $messageKey = 'failMessage';
    private $oldData = 'oldData';
    
    public function __construct(string $attribute = 'errors', string $csrfAttribute = '__csrf', string $segment = '')
    {
        $this->attribute = $attribute;
        $this->csrfAttribute = $csrfAttribute;
        $this->segment = $segment;
    }
    
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($request->getMethod() !== 'POST') {
            return $handler->handle($request);
        }
        $session = $this->extractSession($request, $this->sessionAttribute);
        $token = $session->getCsrfToken();
        $referer = $request->getHeaderLine('referer');
        $redirect = empty($referer) ? '/' : $referer;
        $attributes = $request->getParsedBody();
        if (!isset($attributes[$this->csrfAttribute])) {
            $this->setFlash($session, $this->messageKey, 'Invalid form data', $this->segment);
            $this->setFlash($session, $this->oldData, $attributes, $this->segment);
            return new RedirectResponse($redirect);
        }
        if (!$token->isValid($attributes[$this->csrfAttribute])) {
            $this->setFlash($session, $this->messageKey, 'Outdated form data', $this->segment);
            $this->setFlash($session, $this->oldData, $attributes, $this->segment);
            return new RedirectResponse($redirect);
        }
        return $handler->handle($request);
    }
}
