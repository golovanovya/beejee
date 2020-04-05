<?php

namespace App\Controller;

use App\Models\Paginator;
use League\Route\Http\Exception\NotFoundException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class JobList extends BasicJobController
{
    private const PER_PAGE = 3;
    
    public function action(ServerRequestInterface $request, array $args = []): ResponseInterface
    {
        $page = isset($args['page']) ? intval($args['page']) : 1;
        $sort = $args['sort'] ?? '';
        $direction = isset($args['direction']) && $args['direction'] === '-' ?
            SORT_DESC :
            SORT_ASC;
        
        $paginator = new Paginator(
            $this->jobRepository->countAll(),
            $page,
            self::PER_PAGE
        );
        
        if ($paginator->getPagesCount() < $page || $page < 1) {
            throw new NotFoundException("Page with this number not found!");
        }
        
        $jobs = $this->jobRepository->all(
            $sort,
            $direction,
            $paginator->getLimit(),
            $paginator->getOffset()
        );
        
        return $this->render('app/index', [
            'jobs' => $jobs,
            'pager' => $paginator,
            'sort' => $sort,
            'direction' => $direction,
        ]);
    }
}
