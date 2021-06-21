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
     * @param Request $request
     * @param int $limit
     * @return Paginator
     */
    public function paginate($query, int $request, int $limit): Paginator
    {
        $paginator = new Paginator($query);
        $paginator
            ->getQuery()
            ->setFirstResult($limit * ($request - 1))
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