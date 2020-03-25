<?php

namespace App\Controller;

use App\Entity\Job;
use App\Models\JobForm;
use App\Models\JobRepository;
use App\Models\Pagination;
use Laminas\Diactoros\Response;
use Laminas\Diactoros\Response\RedirectResponse;
use League\Plates\Engine;
use League\Route\Http\Exception\NotFoundException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Controller for management jobs
 */
class JobController extends BaseController
{
    const PER_PAGE = 3;
    
    private $jobRepository;
    
    public function __construct(Engine $templateRenderer, JobRepository $jobRepository)
    {
        parent::__construct($templateRenderer);
        $this->jobRepository = $jobRepository;
    }
    
    /**
     * Action to show a list of jobs
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function indexAction(ServerRequestInterface $request): ResponseInterface
    {
        $segment = $this->getSession($request);
        $flashMsg = [
            'success' => $segment->getFlash('successMessage'),
            'fail' => $segment->getFlash('failMessage'),
        ];
        $q = $request->getQueryParams();
        $page = $q['page'] ?? 0;
        $order = $q['sort'] ?? '';
        
        $pager = new Pagination(
            $this->jobRepository->countAll(),
            (int)$page ?: 1,
            self::PER_PAGE,
            $q
        );
        
        $jobs = $this->jobRepository->all(
            $pager->getOffset(),
            $pager->getLimit(),
            $order
        );
        
        return new Response($this->templateRenderer('app/index', [
            'jobs' => $jobs,
            'pager' => $pager,
            'isAdmin' => $this->isAdmin,
            'flashMsg' => $flashMsg,
            'q' => $q,
        ]));
    }
    
    /**
     * Action to show a job form
     * @param ServerRequestInterface $request
     * @param [] $args
     * @return ResponseInterface
     */
    public function jobFormAction(ServerRequestInterface $request, $args): ResponseInterface
    {
        $segment = $this->getSession($request);
        $model = $segment->getFlash('oldData');
        if (!$model && isset($args['id'])) {
            $id = (int)$args['id'] ?? null;
            $model = $this->getModel($id);
        } elseif (!$model) {
            $model = new JobForm();
        }
        
        return new Response($this->templateRenderer('app/form', [
            'model' => $model,
            'isAdmin' => $this->isAdmin,
        ]));
    }
    
    /**
     * Action to create a job
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function createAction(ServerRequestInterface $request): ResponseInterface
    {
        $segment = $this->getSession($request);
        
        $model = new JobForm();
        
        if ($model->load($request->getParsedBody()) && $model->validate()) {
            $job = new Job($model->getDto());
            $job->loadForm($model);
            if ($this->jobRepository->save($job)) {
                $segment->setFlash('successMessage', 'Задача добавлена');
            } else {
                $segment->setFlash('failMessage', 'Произошла ошибка. Задача не создана.');
            }
            return new RedirectResponse('/');
        } else {
            $segment->setFlash('oldData', $model);
            return new RedirectResponse("/create");
        }
    }
    
    /**
     * Action to update a job
     * @param ServerRequestInterface $request
     * @param [] $args
     * @return ResponseInterface
     */
    public function updateAction(ServerRequestInterface $request, $args): ResponseInterface
    {
        $id = (int)$args['id'] ?? null;
        $segment = $this->getSession($request);
        $model = $this->getModel($id);
        
        if ($model->load($request->getParsedBody()) && $model->validate()) {
            $job->loadForm($model);
            if ($this->jobRepository->save($job)) {
                $segment->setFlash('successMessage', 'Задача сохранена');
            } else {
                $segment->setFlash('failMessage', 'Произошла ошибка. Задача не сохранена.');
            }
            return new RedirectResponse('/');
        } else {
            $segment->setFlash('oldData', $model);
            return new RedirectResponse("/update/$id");
        }
    }
    
    /**
     * Get model by ID
     * @param int $id
     * @return JobForm
     * @throws NotFoundException
     */
    private function getModel(int $id)
    {
        $job = $this->jobRepository->find($id);
        if (!$job) {
            throw new NotFoundException("Задача не найдена");
        }
        return new JobForm($job->getDto());
    }
}
