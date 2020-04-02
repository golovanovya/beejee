<?php

namespace App\Controller;

use App\Models\Pagination;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class JobList extends BasicJobController
{
    private const PER_PAGE = 3;
    
    public function action(ServerRequestInterface $request, array $args = []): ResponseInterface
    {
        $q = $request->getQueryParams();
        $page = $q['page'] ?? 0;
        $order = $q['sort'] ?? '';
        
        $pager = new Pagination(
            $this->jobRepository->countAll(),
            intval($page) ?: 1,
            self::PER_PAGE,
            $q
        );
        
        $jobs = $this->jobRepository->all(
            $pager->getOffset(),
            $pager->getLimit(),
            $order
        );
        
        return $this->render('app/index', [
            'jobs' => $jobs,
            'pager' => $pager,
            'q' => $q,
        ]);
    }
}
