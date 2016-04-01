<?php

namespace Wynajem\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Wynajem\AdminBundle\Form\PostType;
use Wynajem\BlogBundle\Entity\Post;
use Wynajem\BlogBundle\Entity\Category;


class PostsController extends Controller
{

    private $delete_token_name = "delete-post-%d";

    /**
     * @Route(
     *      "/list/{status}/{page}",
     *      name="admin_postsList",
     *      requirements={"page"="\d+"},
     *      defaults={"status"="all", "page"=1}
     * )
     * @param Request $request
     * @param $status
     * @param $page
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request, $status, $page)
    {

        $queryParams = array(
            'titleLike' => $request->query->get('titleLike'),
            'categoryId' => $request->query->get('categoryId'),
            'status' => $status,
        );

        $PostRepository = $this->getDoctrine()->getRepository('WynajemBlogBundle:Post');
        $statistics = $PostRepository->getStatistics();
        $qb = $PostRepository->getQueryBuilder($queryParams);

        $paginationLimit = $this->container->getParameter('admin.pagination_limit');
        $limits = array(2, 5, 10, 15);

        $limit = $request->query->get('limit', $paginationLimit);

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate($qb, $page, $limit);
        $categoriesList = $this->getDoctrine()->getRepository('WynajemBlogBundle:Category')->getAsArray();

        $statusesList = array(
            'Wszystkie' => 'all',
            'Opublikowane' => 'published',
            'Nieopublikowane' => 'unpublished',
        );

        return $this->render(
            'WynajemAdminBundle:Posts:index.html.twig',
            array(
                'currPage' => 'posts',
                'queryParams' => $queryParams,
                'categoriesList' => $categoriesList,

                'limits' => $limits,
                'currLimit' => $limit,

                'statusesList' => $statusesList,
                'currStatus' => $status,
                'statistics' => $statistics,

                'pagination' => $pagination,

                'deleteTokenName' => $this->delete_token_name,
                'csrfProvider' => $this->get('security.csrf.token_manager'),
            )
        );
    }

    /**
     * @Route(
     *      "/form/{id}",
     *      name="admin_postForm",
     *      requirements={"id"="\d+"},
     *      defaults={"id"=NULL}
     * )
     *
     */
    public function formAction(Request $Request, $id = null)
    {
        if (null == $id) {
            $Post = new Post();
            $Post->setAuthor($this->getUser());
            $newPostForm = true;
        } else {
            $Post = $this->getDoctrine()->getRepository('WynajemBlogBundle:Post')->find($id);
        }
        $form = $this->createForm(new PostType(), $Post);
        $form->handleRequest($Request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($Post);
            $em->flush();

            $message = (isset($newPostForm)) ? 'Poprawnie dodano nowy post!' : 'Post został poprawiony!';
            $this->get('session')->getFlashBag()->add('success', $message);

            return $this->redirect(
                $this->generateUrl(
                    'admin_postForm',
                    array(
                        'id' => $Post->getId(),
                    )
                )
            );
        }

        return $this->render(
            'WynajemAdminBundle:Posts:form.html.twig',
            array(
                'currPage' => 'posts',
                'form' => $form->createView(),
                'post' => $Post,
            )
        );
    }

    /**
     * @Route(
     *      "/delete/{id}/{token}",
     *      name="admin_postDelete",
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

            $Post = $this->getDoctrine()->getRepository('WynajemBlogBundle:Post')->find($id);
            $em = $this->getDoctrine()->getManager();
            $em->remove($Post);
            $em->flush();

            $this->get('session')->getFlashBag()->add(
                'success',
                'Poprawnie usunięto post wraz ze wszystkimi komentarzami'
            );
        }

        return $this->redirect($this->generateUrl('admin_postsList'));
    }
}
