<?php

namespace Wynajem\BlogBundle\Controller;

use Wynajem\BlogBundle\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Wynajem\BlogBundle\Entity\Post;
use Wynajem\BlogBundle\Entity\Product;
use Wynajem\BlogBundle\Entity\Tag;

class TaxonomyController extends Controller
{

    protected $itemsLimit = 4;
    /**
     * @Route("/category/{slug}/{page}",
     *      name="blog_category",
     *      defaults={"page" = 1},
     *      requirements={"page" = "\d+"}
     * )
     */
    public function categoryNewsAction($slug, $page)
    {

        $CategoryRepo = $this->getDoctrine()->getRepository(Category::class);
        $Category = $CategoryRepo->findOneBySlug($slug);

        if (null === $Category) {
            throw $this->createNotFoundException('Kategoria nie została znaleziona');
        }
        $pagination = $this->getPaginationProduct(
            array(
                'status' => 1,
                'orderBy' => 'p.addDate',
                'orderDir' => 'DESC',
                'categorySlug' => $slug,
            ),
            $page
        );

        return $this->render(
            'WynajemBlogBundle:Products:offert.html.twig',
            array(
                'pagination' => $pagination,
                'listTitle' => sprintf('Produkty w kategorii: "%s"', $Category->getName()),
            )
        );
    }

    /**
     * @Route("/tag/{slug}/{page}",
     *      name="blog_tag",
     *      defaults={"page" = 1},
     *      requirements={"page" = "\d+"}
     * )
     */
    public function tagNewsAction($slug, $page)
    {

        $TagRepo = $this->getDoctrine()->getRepository(Tag::class);
        $Tag = $TagRepo->findOneBySlug($slug);

        if (null === $Tag) {
            throw $this->createNotFoundException('Tag nie został znaleziony');
        }
        $pagination = $this->getPaginationProduct(
            array(
                'status' => 1,
                'orderBy' => 'p.addDate',
                'orderDir' => 'DESC',
                'tagSlug' => $slug,
            ),
            $page
        );

        return $this->render(
            'WynajemBlogBundle:Products:offert.html.twig',
            array(
                'pagination' => $pagination,
                'listTitle' => sprintf('Produkty w tagu: "%s"', $Tag->getName()),
            )
        );
    }

    public function getPaginationPost(array $params = array(), $page)
    {
        $PostRepo = $this->getDoctrine()->getRepository(Post::class);
        $qb = $PostRepo->getQueryBuilder($params);
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate($qb, $page, $this->itemsLimit);

        return $pagination;
    }
    public function getPaginationProduct(array $params = array(), $page)
    {
        $ProdRepo = $this->getDoctrine()->getRepository(Product::class);
        $qb = $ProdRepo->getQueryBuilder($params);
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate($qb, $page, $this->itemsLimit);
        $totalPages = $ProdRepo->getStatistics();

        if ($page > $totalPages) {
            return $this->redirect($this->generateUrl('blog_news'));
        }

        return $pagination;
    }
}
