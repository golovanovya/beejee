<?php

namespace App\Controller;

use App\Models\LoginForm as Form;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class LoginForm extends BasicRenderController
{
    public function action(ServerRequestInterface $request, array $args = []): ResponseInterface
    {
        $oldData = $request->getAttribute('oldData');
        $model = new Form($oldData !== null ? $oldData : []);
        return $this->render('app/loginForm', [
            'model' => $model,
            'errors' => $request->getAttribute('errors'),
        ]);
    }
}
