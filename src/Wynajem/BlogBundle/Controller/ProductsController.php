<?php

namespace Wynajem\BlogBundle\Controller;

use Common\UserBundle\Form\LoginType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
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
            return $this->redirect($this->generateUrl('blog_offert'));
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
     * @Route("/prices", name="blog_prices")
     *
     */
    public function pricesAction()
    {
        $Session = $this->get('session');
        $loginForm = $this->createForm(
            LoginType::class,
            array(
                'username' => $Session->get(Security::LAST_USERNAME),
            )
        );

        return $this->render('WynajemBlogBundle:Products:prices.html.twig', array(
            'loginForm2' => $loginForm->createView(),
        ));
    }


    /**
     * @Route("/product/{slug}", name="blog_product")
     *
     */
    public function productAction($slug)
//  public function productAction($slug, ProductRepository $productRepository)
    {

//         Metoda bez wstrzykiwania do parametrów funkcji

        $Product = $this->getDoctrine()->getRepository(Product::class);
        $ProductDesc = $Product->findOneBySlug($slug);

        //Metoda z wstrzykiwaniem
//        $ProductDescription= $productRepository->findOneBySlug($slug);

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
        $PostRepo = $this->getDoctrine()->getRepository(Product::class);
        $qb = $PostRepo->getQueryBuilder($params);

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate($qb, $page, $this->itemsLimit);


        return $pagination;
    }



}
