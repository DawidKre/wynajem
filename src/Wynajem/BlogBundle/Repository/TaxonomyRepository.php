<?php
/**
 * Created by PhpStorm.
 * User: Bliksem
 * Date: 2016-02-01
 * Time: 18:02
 */

namespace Wynajem\BlogBundle\Repository;

use Doctrine\ORM\EntityRepository;

class TaxonomyRepository extends EntityRepository
{

    public function getAsArray() {
        return $this->createQueryBuilder('t')
            ->select('t.id, t.name')
            ->getQuery()
            ->getArrayResult();
    }
    public function getQueryBuilder(array $params = array()) {
        $qb = $this->createQueryBuilder('t');

        $qb->select('t, COUNT(p.id) as postsCount')
            ->leftJoin('t.posts', 'p')
            ->groupBy('t.id');
        return $qb;
    }


    public function getPostCountBuilder(){

        $qb =$this->createQueryBuilder('c')
            ->select('t', 'COUNT(posts.id) as postsCount')
            ->leftJoin('t.posts', 'posts')
            ->groupBy('t.id');

        return $qb;

    }

    public function getProductCountBuilder(){

        $qb =$this->createQueryBuilder('t')
            ->select('t', 'COUNT(products.id) as productsCount')
            ->leftJoin('t.products', 'products')
            ->groupBy('t.id');


        return $qb;

    }

}