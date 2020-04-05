<?php

namespace App\Middleware;

use App\SessionTrait;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ExtractFlashNotice implements MiddlewareInterface
{
    use SessionTrait;
    
    private $attribute = 'notice';
    private $errorAttribute = 'failMessage';
    private $successAttribute = 'successMessage';
    private $sessionAttribute = 'session';
    private $segment;
    
    public function __construct(string $segment = '')
    {
        $this->segment = $segment;
    }
    
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $session = $this->extractSession($request, $this->sessionAttribute);
        $messageError = $this->getFlash($session, $this->errorAttribute, $this->segment);
        $messageSuccess = $this->getFlash($session, $this->successAttribute, $this->segment);
        if ($messageError !== null) {
            $request = $request->withAttribute($this->attribute, new \App\Notice($messageError, 'error'));
        } elseif ($messageSuccess !== null) {
            $request = $request->withAttribute($this->attribute, new \App\Notice($messageSuccess));
        }
        return $handler->handle($request);
    }
}
