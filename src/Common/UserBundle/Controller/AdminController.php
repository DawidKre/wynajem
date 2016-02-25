<?php

namespace Common\UserBundle\Controller;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

use Common\UserBundle\Entity\User;
use Common\UserBundle\Form\ManageUserType;


class AdminController extends BaseController
{
    /**
     * @Route(
     *      "/list/{page}",
     *      name="user_adminUsersList",
     *      requirements={"page"="\d+"},
     *      defaults={"page"=1}
     * )
     *
     */
    public function indexAction(Request $Request, $page)
    {

        $UserRepository = $this->getDoctrine()->getRepository('CommonUserBundle:User');

        $qb = $UserRepository->getQueryBuilder();

        //$Request = $this->getRequest();
        $limit = $Request->query->getInt('limit', $this->container->getParameter('admin.pagination_limit'));

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate($qb, $page, $limit);

        return $this->render(
            'CommonUserBundle:Admin:index.html.twig',
            array(
                'currPage' => 'users',
                'pagination' => $pagination,

            )
        );
    }

    /**
     * @Route(
     *      "/form/{id}",
     *      name="user_adminUserForm",
     *      requirements={"id"="\d+"}
     * )
     *
     */
    public function formAction(Request $Request, User $User)
    {

        $form = $this->createForm(new ManageUserType(), $User);

        $form->handleRequest($Request);

        if ($form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($User);
            $em->flush();

            $this->getSessionFlashBag()->add('success', 'Zaktualizowano konto użytkownika');

            return $this->redirect(
                $this->generateUrl(
                    'user_adminUserForm',
                    array(
                        'id' => $User->getId(),
                    )
                )
            );
        }


        return $this->render(
            'CommonUserBundle:Admin:form.html.twig',
            array(
                'currPage' => 'users',
                'form' => $form->createView(),
                'user' => $User,
            )
        );
    }

}
