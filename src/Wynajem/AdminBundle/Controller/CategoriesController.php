<?php

namespace Wynajem\AdminBundle\Controller;

use Common\UserBundle\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

use Wynajem\BlogBundle\Entity\Category;
use Wynajem\AdminBundle\Form\CategoryDeleteType;
use Wynajem\AdminBundle\Form\TaxonomyType;

class CategoriesController extends BaseController
{
    /**
     * @Route(
     *      "/list/{page}",
     *      name="admin_categoriesList",
     *      defaults={"page"=1}
     * )
     *
     */
    public function indexAction($page)
    {

        $CategoryRepository = $this->getDoctrine()->getRepository('WynajemBlogBundle:Category');
        $qb = $CategoryRepository->getProductCountBuilder();

        $limit = $this->container->getParameter('admin.pagination_limit');

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate($qb, $page, $limit);


        return $this->render(
            'WynajemAdminBundle:Categories:index.html.twig',
            array(
                'currPage' => 'taxonomies',
                'pagination' => $pagination,
            )
        );
    }

    /**
     * @Route(
     *      "/form/{id}",
     *      name="admin_categoryForm",
     *      requirements={"id"="\d+"},
     *      defaults={"id"=NULL}
     * )
     *
     */
    public function formAction(Request $Request, Category $Category = null)
    {

        if (null === $Category) {
            $Category = new Category();
            $newCategory = true;
        }

        $form = $this->createForm(new TaxonomyType(), $Category);

        $form->handleRequest($Request);

        if ($form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($Category);
            $em->flush();

            $message = (isset($newCategory)) ? 'Poprawnie dodano nową kategorię!' : 'Poprawiono dane kategorii';
            $this->get('session')->getFlashBag()->add('success', $message);

            return $this->redirect(
                $this->generateUrl(
                    'admin_categoryForm',
                    array(
                        'id' => $Category->getId(),
                    )
                )
            );
        }

        return $this->render(
            'WynajemAdminBundle:Categories:form.html.twig',
            array(
                'currPage' => 'taxonomies',
                'form' => $form->createView(),
                'category' => $Category,
            )
        );
    }

    /**
     * @Route(
     *      "/delete/{id}",
     *      name="admin_categoryDelete"
     * )
     *
     */
    public function deleteAction(Request $Request, Category $Category)
    {

        $form = $this->createForm(new CategoryDeleteType($Category));

        $form->handleRequest($Request);

//        if ($form->getData() !== $Category->getId()) {
//            throw new Exception('Taka kategoria juz jest');
//        }

        if ($form->isValid()) {

            $chosen = false;

            if (true === $form->get('setNull')->getData()) {
                $newCategoryId = null;
                $chosen = true;
            } else {
                if (null !== ($NewCategory = $form->get('newCategory')->getData())) {
                    $newCategoryId = $NewCategory->getId();
                    $chosen = true;
                }
            }

            if ($chosen) {
                $PostRepo = $this->getDoctrine()->getRepository('WynajemBlogBundle:Post');
                $modifiedPosts = $PostRepo->moveToCategory($Category->getId(), $newCategoryId);
                $em = $this->getDoctrine()->getManager();
                $em->remove($Category);
                $em->flush();

                $Request->get('session')
                    ->getFlashBag()
                    ->add(
                        'success',
                        sprintf('Kategoria została usunięta. %d postów zostało zmodyfikowanych.', $modifiedPosts)
                    );

                return $this->redirect($this->generateUrl('admin_categoriesList'));

            } else {
                $Request->get('session')
                    ->getFlashBag()
                    ->add('error', 'Musisz wybrać nowa kategorię lub zaznaczyć checkbox!');
            }

        }

        return $this->render(
            'WynajemAdminBundle:Categories:delete.html.twig',
            array(
                'currPage' => 'taxonomies',
                'category' => $Category,
                'form' => $form->createView(),
            )
        );
    }

}
