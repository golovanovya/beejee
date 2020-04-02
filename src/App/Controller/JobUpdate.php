<?php

namespace App\Controller;

use App\Entity\Job;
use App\Models\JobForm;
use App\SessionTrait;
use Laminas\Diactoros\Response\RedirectResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class JobUpdate extends BasicJobController
{
    use SessionTrait;
    
    public function action(ServerRequestInterface $request, array $args = []): ResponseInterface
    {
        $id = intval($args['id']);
        $session = $this->extractSession($request);
        
        $job = $this->getModel($id);
        $job->loadForm(new JobForm($request->getParsedBody()));
        
        if ($this->jobRepository->save($job)) {
            $this->setFlash($session, 'successMessage', 'Задача добавлена');
        } else {
            $this->setFlash($session, 'failMessage', 'Произошла ошибка. Задача не создана.');
        }
        
        return new RedirectResponse('/');
    }
}
