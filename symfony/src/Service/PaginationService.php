<?php

namespace App\Service;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpFoundation\Request;

class PaginationService
{
    /**
     * @param QueryBuilder|Query $query
     * @param integer $page
     * @return Paginator
     */
    public function paginate($query, int $page, int $limit): Paginator
    {
        $paginator = new Paginator($query);
        $paginator
            ->getQuery()
            ->setFirstResult($limit * ($page - 1))
            ->setMaxResults($limit);

        return $paginator;
    }

    /**
     * @param Paginator $paginator
     * @return int
     */
    public function lastPage(Paginator $paginator): int
    {
        return ceil($paginator->count() / $paginator->getQuery()->getMaxResults());
    }
}