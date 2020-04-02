<?php

namespace App\Middleware;

use App\SessionTrait;
use App\User;
use App\UserManager;
use InvalidArgumentException;
use Middlewares\AuraSession;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class SessionAuthenticate implements MiddlewareInterface
{
    use SessionTrait;
    
    private $attribute = 'user';
    private $segment;
    private $sessionAttribute = 'session';
    private $userManager;
    
    public function __construct(UserManager $userManager, string $segment = '')
    {
        $this->userManager = $userManager;
        $this->segment = $segment;
    }
    
    /**
     * Process a server request and return a response.
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        /* @var $session AuraSession */
        $session = $request->getAttribute($this->sessionAttribute);
        
        if ($session === null) {
            throw new InvalidArgumentException('Request must contain a session attribute');
        }
        $username = $this->getSessionData($session, User::KEY, $this->segment);
        if ($username === null) {
            return $handler->handle($request);
        }
        $user = $this->userManager->findUser($username);
        if ($user !== null) {
            $request = $request->withAttribute($this->attribute, $user);
        }
        return $handler->handle($request);
    }
}
