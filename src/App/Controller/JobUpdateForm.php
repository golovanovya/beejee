<?php

namespace App\Controller;

use App\Models\JobForm as Form;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class JobUpdateForm extends BasicJobController
{
    public function action(ServerRequestInterface $request, array $args = []): ResponseInterface
    {
        $oldData = $request->getAttribute('oldData');
        $id = intval($args['id']);
        $job = $this->getModel($id);
        $formModel = new Form();
        $formModel->load($oldData ?? $job->getDto());
        return static::buildResponse($this->render('app/jobForm', [
            'model' => $formModel,
            'errors' => $request->getAttribute('errors'),
            'id' => $id,
        ]));
    }
}
