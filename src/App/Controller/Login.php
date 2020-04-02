<?php

namespace App\Controller;

use App\SessionTrait;
use App\User;
use App\UserManager;
use Laminas\Diactoros\Response\RedirectResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Login
{
    use SessionTrait;
    
    private $segment = '';
    private $userManager;

    public function __construct(UserManager $userManager, string $segment = '')
    {
        $this->userManager = $userManager;
        $this->segment = $segment;
    }
    
    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $args = $request->getParsedBody();
        $user = $this->userManager->findUser($args['login']);
        $session = $this->extractSession($request);
        if ($user !== null && $this->userManager->validatePassword($user, $args['password'])) {
            $this->setSessionData($session, User::KEY, $args['login'], $this->segment);
            return new RedirectResponse('/');
        }
        $this->setFlash($session, 'errors', ['login' => 'Invalid login or password'], $this->segment);
        $this->setFlash($session, 'oldData', $args, $this->segment);
        return new RedirectResponse('/login');
    }
}
