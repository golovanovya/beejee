<?php

namespace App\Controller;

use App\SessionTrait;
use App\User;
use Mezzio\Authentication\UserRepositoryInterface;
use Laminas\Diactoros\Response\RedirectResponse;
use Laminas\Diactoros\Uri;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Login
{
    use SessionTrait;
    
    private $segment = '';
    private $userManager;

    public function __construct(UserRepositoryInterface $userManager, string $segment = '')
    {
        $this->userManager = $userManager;
        $this->segment = $segment;
    }
    
    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $attributes = $request->getParsedBody();
        $args = $request->getQueryParams();
        $session = $this->extractSession($request);
        /* @var $referer Uri */
        $referer = new Uri($request->hasHeader('referer') ? $request->getHeaderLine('referer') : '/login');
        /* @var $redirect Uri */
        $redirect = new Uri(isset($args['redirect']) ? $args['redirect'] : '/');
        $user = $this->userManager->authenticate($attributes['login'], $attributes['password']);
        if ($user !== null) {
            $this->setSessionData($session, User::KEY, $attributes['login'], $this->segment);
            return new RedirectResponse($redirect);
        }
        $this->setFlash($session, 'errors', ['login' => 'Invalid login or password'], $this->segment);
        $this->setFlash($session, 'oldData', $attributes, $this->segment);
        return new RedirectResponse($referer->withQuery('redirect=' . rawurlencode($redirect)));
    }
}
