<?php

namespace Wynajem\BlogBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * PostRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PostRepository extends EntityRepository
{

    public function getPublishedPost($slug)
    {
        $qb = $this->getQueryBuilder(array(
            'status'    =>  'published'
        ));

        $qb->andWhere('p.slug = :slug')
            ->setParameter('slug', $slug);

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function getQueryBuilder(array $params = array())
    {
        $qb =$this->createQueryBuilder('p')
            ->select('p','c','t','a')
            ->leftJoin('p.category', 'c')
            ->leftJoin('p.tags','t')
            ->leftJoin('p.author', 'a');

        if(!empty($params['status'])){
            if('published' == $params['status']){
                $qb->where('p.publishedDate <= :currentDate AND p.publishedDate is NOT NULL')
                    ->setParameter('currentDate', new \DateTime());

            }elseif('unpublished' == $params['status']){
                $qb->where('p.publishedDate > :currDate OR p.publishedDate IS NULL')
                    ->setParameter('currDate', new \DateTime());
            }
        }

        if(!empty($params['orderBy'])){
            $orderDir = !empty($params['orderDir']) ? $params['orderDir'] : NULL;
            $qb->orderBy($params['orderBy'], $orderDir);
        }


        if(!empty($params['categorySlug'])){
            $qb->andWhere('c.slug = :categorySlug')
                ->setParameter('categorySlug', $params['categorySlug']);
        }

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

        if(!empty($params['tagSlug'])){
            $qb->andWhere('t.slug = :tagSlug')
                ->setParameter('tagSlug', $params['tagSlug']);
        }

        if(!empty($params['postAuthor'])){
            $qb->andWhere('p.author = :postAuthor')
                ->setParameter('postAuthor', $params['postAuthor']);
        }

        if(!empty($params['search'])){
            $searchParam = '%'.$params['search'].'%';
            $qb->andWhere('p.title LIKE :searchParam OR p.content LIKE :searchParam')
                ->setParameter('searchParam', $searchParam);
        }

        if(!empty($params['titleLike'])){
            $titleLike = '%'.$params['titleLike'].'%';
            $qb->andWhere('p.title LIKE :titleLike')
                ->setParameter('titleLike', $titleLike);
        }


        return $qb;

    }

    public function getStatistics(){
        $qb = $this->createQueryBuilder('p')
            ->select('COUNT(p)');


        $all = (int)$qb->getQuery()->getSingleScalarResult();

        $published = (int)$qb->andWhere('p.publishedDate <= :currDate AND p.publishedDate IS NOT NULL')
            ->setParameter('currDate', new \DateTime())
            ->getQuery()
            ->getSingleScalarResult();

        return array(
            'all' => $all,
            'published' => $published,
            'unpublished' => ($all - $published)
        );
    }

    public function getPublishedCount(){
        $qb = $this->createQueryBuilder('p')
            ->select('COUNT(p)')
            ->where('p.publishedDate <= :currDate AND p.publishedDate is NOT NULL')
            ->setParameter('currDate', new \DateTime());

        $all = (int)$qb->getQuery()->getSingleScalarResult();

        return $all;
    }

    public function getRecentCommented($limit = 5 )
    {
        $qb = $this->createQueryBuilder('p')
            ->select('p.title, p.slug, COUNT(c) as commentsCount')
            ->leftJoin('p.comments', 'c')
            ->groupBy('p.title')
            ->having('commentsCount > 0')
            ->where('p.publishedDate <= :currDate AND p.publishedDate IS NOT NULL' )
            ->setParameter('currDate', new \DateTime())
            ->orderBy('commentsCount', 'DESC')
            ->setMaxResults($limit);

        return $qb->getQuery()->getArrayResult();
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
