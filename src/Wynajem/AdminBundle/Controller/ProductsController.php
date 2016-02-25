<?php

namespace Wynajem\AdminBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Wynajem\AdminBundle\Form\ProductType;
use Wynajem\BlogBundle\Entity\Product;
use Symfony\Component\HttpFoundation\Request;


class ProductsController extends Controller
{

    private $delete_token_name = "delete-product-%d";

    /**
     * @Route(
     *     "/list/{status}/{page}",
     *     name="admin_productsList",
     *     requirements={"page"="\d+"},
     *     defaults={"status"="all", "page"=1}
     * )
     */
    public function indexAction(Request $request, $status, $page)
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
            'WynajemAdminBundle:Product:index.html.twig',
            array(
                'currPage' => 'posts',
                'queryParams' => $queryParams,
                'categoriesList' => $categoriesList,

                'limits' => $limits,
                'currLimit' => $limit,

                'statusesList' => $statusList,
                'currStatus' => $status,
                'statistics' => $statistic,

                'pagination' => $pagination,

                'deleteTokenName' => $this->delete_token_name,
                'csrfProvider' => $this->get('security.csrf.token_manager'),
            )
        );
    }


    /**
     * @Route(
     *      "/form/{id}",
     *      name="admin_productForm",
     *      requirements={"id"="\d+"},
     *      defaults={"id"=NULL}
     * )
     *
     */
    public function formAction(Request $request, $id = null)
    {

        if (null == $id) {
            $Product = new Product();
//            $Post->setAuthor($this->getUser());
            $newProductForm = true;
        } else {
            $Product = $this->getDoctrine()->getRepository('WynajemBlogBundle:Product')->find($id);
        }

        $form = $this->createForm(new ProductType(), $Product);

        $form->handleRequest($request);
        if ($form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($Product);
            $em->flush();

            $message = (isset($newProductForm)) ? 'Poprawnie dodano nowy product!' : 'Produkt został poprawiony!';
            $this->get('session')->getFlashBag()->add('success', $message);

            return $this->redirect(
                $this->generateUrl(
                    'admin_productForm',
                    array(
                        'id' => $Product->getId(),
                    )
                )
            );
        }
        return $this->render(
            'WynajemAdminBundle:Product:form.html.twig',
            array(
                'currPage' => 'products',
                'form' => $form->createView(),
                'product' => $Product,
            )
        );

    }
    /**
     * @Route(
     *      "/delete/{id}/{token}",
     *      name="admin_productDelete",
     *      requirements={"id"="\d+"}
     * )
     *
     */
    public function deleteAction($id, $token)
    {

        $tokenName = sprintf($this->delete_token_name, $id);
        $csrfProvider = $this->get('form.csrf_provider');

        if (!$csrfProvider->isCsrfTokenValid($tokenName, $token)) {
            $this->get('session')->getFlashBag()->add('error', 'Niepoprawny token akcji!');

        } else {

            $Post = $this->getDoctrine()->getRepository('WynajemBlogBundle:Product')->find($id);
            $em = $this->getDoctrine()->getManager();
            $em->remove($Post);
            $em->flush();

            $this->get('session')->getFlashBag()->add(
                'success',
                'Poprawnie usunięto produkt'
            );
        }

        return $this->redirect($this->generateUrl('admin_productsList'));
    }


}
