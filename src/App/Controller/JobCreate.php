<?php

namespace App\Controller;

use App\Entity\Job;
use App\Models\JobForm;
use App\SessionTrait;
use Laminas\Diactoros\Response\RedirectResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class JobCreate extends BasicJobController
{
    use SessionTrait;
    
    public function action(ServerRequestInterface $request, array $args = []): ResponseInterface
    {
        $model = new JobForm();
        $session = $this->extractSession($request);
        
        $model->load($request->getParsedBody());
        $job = new Job($model->getDto());
        $job->loadForm($model);
        if ($this->jobRepository->save($job)) {
            $this->setFlash($session, 'successMessage', 'Задача добавлена');
        } else {
            $this->setFlash($session, 'failMessage', 'Произошла ошибка. Задача не создана.');
        }
        return new RedirectResponse('/');
    }
}
