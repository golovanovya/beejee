<?php

namespace App\Models;

class Paginator
{
    private $totalCount;
    private $page;
    private $limit;

    public function __construct(int $totalCount, int $page, int $perPage)
    {
        $this->totalCount = $totalCount;
        $this->page = $page;
        $this->limit = $perPage;
    }

    public function getTotalCount(): int
    {
        return $this->totalCount;
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function getPagesCount(): int
    {
        return ceil($this->totalCount / $this->limit);
    }

    public function getPerPage(): int
    {
        return $this->limit;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function getOffset(): int
    {
        return ($this->page - 1) * $this->limit;
    }
}
