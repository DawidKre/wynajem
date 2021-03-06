<?php

namespace Wynajem\BlogBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * ProductRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ProductRepository extends EntityRepository
{


    public function getQueryBuilder(array $params = array())
    {
        $qb = $this->createQueryBuilder('p')
            ->select('p', 'c', 't')
            ->leftJoin('p.category', 'c')
            ->leftJoin('p.tags', 't');

        if (!empty($params['available'])) {
            $qb->andWhere('p.available =:available')
                ->setParameter('available', $params['available']);
        }

        if (!empty($params['status'])) {
            if ($params['status'] == 'available') {
                $qb->andWhere('p.available = :available')
                    ->setParameter('available', 1);

            } elseif ($params['status'] == 'unavailable') {
                $qb->andWhere('p.available =:unavailable')
                    ->setParameter('unavailable', 0);
            }

        }
        if (!empty($params['orderBy'])) {
            $orderDir = !empty($params['orderDir']) ? $params['orderDir'] : null;
            $qb->orderBy($params['orderBy'], $orderDir);
        }

        if (!empty($params['categorySlug'])) {
            $qb->andWhere('c.slug = :categorySlug')
                ->setParameter('categorySlug', $params['categorySlug']);
        }

        if (!empty($params['tagSlug'])) {
            $qb->andWhere('t.slug = :tagSlug')
                ->setParameter('tagSlug', $params['tagSlug']);
        }

//        if(!empty($params['postAuthor'])){
//            $qb->andWhere('p.author = :postAuthor')
//                ->setParameter('postAuthor', $params['postAuthor']);
//        }
        if(!empty($params['categoryId'])) {
            if (-1 == $params['categoryId']) {
                $qb->andWhere($qb->expr()->isNull('p.category'));
            }
            elseif (-2 == $params['categoryId']) {
                $qb->andWhere('0 = 0');
            }
            else {
                $qb->andWhere('c.id = :categoryId')
                    ->setParameter('categoryId', $params['categoryId']);

            }
        }

        if (!empty($params['search'])) {
            $searchParam = '%'.$params['search'].'%';
            $qb->andWhere('p.name LIKE :searchParam OR p.description LIKE :searchParam')
                ->setParameter('searchParam', $searchParam);
        }
        if(!empty($params['titleLike'])){
            $titleLike = '%'.$params['titleLike'].'%';
            $qb->andWhere('p.name LIKE :titleLike')
                ->setParameter('titleLike', $titleLike);
        }

        return $qb;

    }

    public function getStatistics()
    {
        $qb = $this->createQueryBuilder('p')
            ->select('COUNT(p)');


        $all = (int)$qb->getQuery()->getSingleScalarResult();

        $available = (int)$qb->andWhere('p.available = :available')
            ->setParameter('available', 1)
            ->getQuery()
            ->getSingleScalarResult();

        return array(
            'all' => $all,
            'available' => $available,
            'unavailable' => ($all - $available),
        );
    }
    public function getTotalPages($itemsLimit)
    {
        $statistic= $this->getStatistics();
        $totalPages = ceil($statistic['all'] / $itemsLimit);
        return $totalPages;
    }

    public function moveToCategory($oldCategoryId, $newCategoryId) {
        return $this->createQueryBuilder('p')
            ->update()
            ->set('p.category', ':newCategoryId')
            ->where('p.category = :oldCategoryId')
            ->setParameters(array(
                'newCategoryId' => $newCategoryId,
                'oldCategoryId' => $oldCategoryId
            ))
            ->getQuery()
            ->execute();
    }
}
