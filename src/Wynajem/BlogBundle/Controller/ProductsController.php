<?php

namespace Wynajem\BlogBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Wynajem\BlogBundle\Entity\Product;

class ProductsController extends Controller
{

    protected $itemsLimit = 9;

    /**
     * @Route("/offert/{page}",
     *      name="blog_offert",
     *     defaults={"page" = 1},
     *     requirements={"page" = "\d+"}
     * )
     *
     */
    public function offertAction($page)
    {
        $ProducRepo = $this->getDoctrine()->getRepository(Product::class);

        $pagination = $this->getProducts(
            array(
                'orderBy' => 'p.addDate',
                'orderDir' => 'DESC',
            ),
            $page
        );

        if ($page > $ProducRepo->getTotalPages($this->itemsLimit)) {
//            return $this->redirect($this->generateUrl('blog_offert'));
            return $this->redirect($this->get('router')->generate('blog_offert'));
        }

        return $this->render(
            'WynajemBlogBundle:Products:offert.html.twig',
            array(
                'pagination' => $pagination,
                'listTitle' => 'Oferta wynajmu',
            )
        );
    }


    /**
     * @Route(
     *     "/prices/{status}/{page}",
     *     name="blog_prices",
     *     requirements={"page"="\d+"},
     *     defaults={"status"="all", "page"=1}
     * )
     * @param Request $request
     * @param $status
     * @param $page
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function pricesAction(Request $request, $status, $page)
    {
        $queryParams = array(
            'status' => $status,
            'titleLike' => $request->query->get('titleLike'),
            'categoryId' => $request->query->get('categoryId'),
        );

        $ProductRepository = $this->getDoctrine()->getRepository(Product::class);
        $statistic = $ProductRepository->getStatistics();

        $qb = $ProductRepository->getQueryBuilder($queryParams);

        $paginationLimit = $this->container->getParameter('admin.pagination_limit');
        $limits = array(2, 5, 10, 15);
        $limit = $request->query->get('limit', $paginationLimit);

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate($qb, $page, $limit);

        $categoriesList = $this->getDoctrine()->getRepository('WynajemBlogBundle:Category')->getAsArray();

        $statusList = array(
            'Dostępne' => 'available',
            'Nie dostępne' => 'unavailable',

        );

        return $this->render(
            'WynajemBlogBundle:Products:prices.html.twig',
            array(
                'currPage' => 'products',
                'queryParams' => $queryParams,
                'categoriesList' => $categoriesList,

                'limits' => $limits,
                'currLimit' => $limit,

                'statusesList' => $statusList,
                'currStatus' => $status,
                'statistics' => $statistic,

                'pagination' => $pagination,
            )
        );
    }


    /**
     * @Route("/product/{slug}", name="blog_product")
     *
     */
    public function productAction($slug)
    {
        $Product = $this->getDoctrine()->getRepository(Product::class);
        $ProductDesc = $Product->findOneBySlug($slug);

        if (null === $Product) {
            throw $this->createNotFoundException('Produkt  nie został znaleziony');
        }

        return $this->render(
            'WynajemBlogBundle:Products:product.html.twig',
            array(
                'product' => $ProductDesc,

            )
        );
    }

    /**
     * @Route("/search/{page}",
     *     name="blog_search",
     *     defaults={"page" = 1},
     *     requirements={"page" = "\d+"}
     * )
     *
     */
    public function searchAction(Request $request, $page)
    {
        $searchParam = $request->query->get('search');

        $pagination = $this->getProducts(
            array(
                'available' => 1,
                'search' => $searchParam,
            ),
            $page
        );

        return $this->render(
            'WynajemBlogBundle:Products:offert.html.twig',
            array(
                'pagination' => $pagination,
                'listTitle' => sprintf('Wyniki wyszukiwania frazy "%s', $searchParam),
                'searchParam' => $searchParam,
            )
        );
    }


    public function getProducts(array $params = array(), $page)
    {
        $ProdRepo = $this->getDoctrine()->getRepository(Product::class);
        $qb = $ProdRepo->getQueryBuilder($params);

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate($qb, $page, $this->itemsLimit);


        return $pagination;
    }


}
