<?php

namespace App\Controller;

use App\Models\LoginForm;
use Laminas\Diactoros\Response\RedirectResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class SiteController extends BaseController
{
    /**
     * @var \App\AuthManager
     */
    private $authManager;
    private $adminUsers;
    
    public function __construct(
        \League\Plates\Engine $templateRenderer,
        \App\AuthManager $authManager,
        array $adminUsers
    ) {
        parent::__construct($templateRenderer);
        $this->authManager = $authManager;
        $this->adminUsers = $adminUsers;
    }
    
    public function loginAction(ServerRequestInterface $request): ResponseInterface
    {
        $users = $this->adminUsers;
        $isPost = $request->getMethod() === 'POST';

        $model = new \App\Models\LoginForm();

        if ($isPost && $model->load($request->getParsedBody()) && $model->validate($users)) {
            $this->authManager->login(new \App\User('admin', 'admin'));
            return new \Laminas\Diactoros\Response\RedirectResponse('/');
        } else {
            return $this->render('app/loginForm', [
                    'model' => $model,
                    'isAdmin' => $this->authManager->isAdmin(),
            ]);
        }
        return new RedirectResponse('/');
    }

    public function logoutAction(ServerRequestInterface $request): ResponseInterface
    {
        $this->authManager->logout($request);

        return new \Laminas\Diactoros\Response\RedirectResponse('/');
    }
}
