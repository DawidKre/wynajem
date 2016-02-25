<?php

namespace Wynajem\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

use Wynajem\BlogBundle\Entity\Tag;
use Wynajem\AdminBundle\Form\TaxonomyType;


class TagsController extends Controller
{

    private $deleteTokenName = 'delete-%d-tag';

    /**
     * @Route(
     *      "/list/{page}",
     *      name="admin_tagsList",
     *      requirements={"page"="\d+"},
     *      defaults={"page"=1}
     * )
     *
     */
    public function indexAction(Request $Request, $page) {

        $TagRepository = $this->getDoctrine()->getRepository('WynajemBlogBundle:Tag');

        $qb = $TagRepository->getProductCountBuilder();

        $limit = $this->container->getParameter('admin.pagination_limit');

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate($qb, $page, $limit);

        return $this->render('WynajemAdminBundle:Tags:index.html.twig', array(
            'currPage' => 'taxonomies',
            'pagination' => $pagination,
            'deleteTokenName' => $this->deleteTokenName,
            'csrfProvider' => $this->get('form.csrf_provider')
        ));
    }
    /**
     * @Route(
     *      "/form/{id}",
     *      name="admin_tagForm",
     *      requirements={"id"="\d+"},
     *      defaults={"id"=NULL}
     * )
     *
     */
    public function formAction(Request $Request, Tag $Tag = NULL) {

        if(NULL === $Tag){
            $Tag = new Tag();
            $newTag = TRUE;
        }

        $form = $this->createForm(new TaxonomyType(), $Tag);

        $form->handleRequest($Request);

        if($form->isValid()){

            $em = $this->getDoctrine()->getManager();
            $em->persist($Tag);
            $em->flush();

            $flashMessage = (isset($newTag)) ? 'Poprawnie dodano nowy tag' : 'Poprawiono tag';

            $this->get('session')->getFlashBag()->add('success', $flashMessage);

            return $this->redirect($this->generateUrl('admin_tagForm', array(
                'id' => $Tag->getId()
            )));

        }

        return $this->render('WynajemAdminBundle:Tags:form.html.twig', array(
            'currPage' => 'taxonomies',
            'form' => $form->createView(),
            'tag' => $Tag
        ));
    }

    /**
     * @Route(
     *      "/delete/{id}/{token}",
     *      name="admin_tagDelete",
     *      requirements={"id"="\d+"}
     * )
     *
     */
    public function deleteAction($id, $token) {

        $tokenName = sprintf($this->deleteTokenName, $id);
        $csrfProvider = $this->get('form.csrf_provider');

        if(!$csrfProvider->isCsrfTokenValid($tokenName, $token)){
            $this->get('session')->getFlashBag()->add('error', 'Niepoprawny token akcji!');

        }else{

            $Tag = $this->getDoctrine()->getRepository('WynajemBlogBundle:Tag')->find($id);
            $em = $this->getDoctrine()->getManager();
            $em->remove($Tag);
            $em->flush();

            $this->get('session')->getFlashBag()->add('success', 'Poprawnie usunięto tag.');
        }

        return $this->redirect($this->generateUrl('admin_tagsList'));
    }
}
