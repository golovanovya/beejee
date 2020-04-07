<?php

namespace App\Controller;

use App\Models\LoginForm as Form;
use Laminas\Diactoros\Uri;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class LoginForm extends BasicRenderController
{
    public function action(ServerRequestInterface $request, array $args = []): ResponseInterface
    {
        $args = $request->getQueryParams();
        $oldData = $request->getAttribute('oldData');
        $model = new Form($oldData !== null ? $oldData : []);
        $loginUri = new Uri('/login');
        $referer = new Uri($request->getHeaderLine('referer'));
        if (isset($args['redirect'])) {
            $loginUri = $loginUri->withQuery('redirect=' . $args['redirect']);
        } elseif ($request->hasHeader('referer') && $referer->getPath() !== $loginUri->getPath()) {
            $loginUri = $loginUri->withQuery('redirect=' . rawurldecode($referer));
        }
        return $this->render('app/loginForm', [
            'model' => $model,
            'errors' => $request->getAttribute('errors'),
            'loginUri' => $loginUri,
        ]);
    }
}
