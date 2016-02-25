<?php

namespace Wynajem\BlogBundle\Controller;

use Common\UserBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Wynajem\BlogBundle\Entity\Post;
use Wynajem\BlogBundle\Entity\Comment;
use Wynajem\BlogBundle\Form\CommentType;


class PostsController extends Controller
{
    protected $itemsLimit = 4;

    /**
     * @Route("/{page}",
     *     name="blog_news",
     *     defaults={"page" = 1},
     *     requirements={"page" = "\d+"}
     * )
     *
     */
    public function newsAction($page)
    {
        $pagination = $this->getPaginationPosts(
            array(
                'status' => 'published',
                'orderBy' => 'p.publishedDate',
                'orderDir' => 'DESC',
            ),
            $page
        );

        $PostRepo = $this->getDoctrine()->getRepository('WynajemBlogBundle:Post');
        $totalPosts = $PostRepo->getPublishedCount();
        $totalPages = ceil($totalPosts / $this->itemsLimit);

        if ($page > $totalPages) {
            return $this->redirect($this->generateUrl('blog_news'));
        }

        return $this->render(
            'WynajemBlogBundle:Posts:newsList.html.twig',
            array(
                'pagination' => $pagination,
                'listTitle' => 'Aktualności',
            )
        );
    }


    /**
     * @Route("/author/{author}/{page}",
     *      name="blog_author",
     *      defaults={"page" = 1},
     *      requirements={"page" = "\d+"}
     * )
     */
    public function authorNewsAction($author, $page)
    {

        $UserRepo = $this->getDoctrine()->getRepository(User::class);
        $User = $UserRepo->findOneByUsername($author);

        if (null === $User) {
            throw $this->createNotFoundException('Autor nie został znaleziony');
        }

        $pagination = $this->getPaginationPosts(
            array(
                'status' => 'published',
                'orderBy' => 'p.publishedDate',
                'orderDir' => 'DESC',
                'postAuthor' => $User,
            ),
            $page
        );


        return $this->render(
            'WynajemBlogBundle:Posts:newsList.html.twig',
            array(
                'pagination' => $pagination,
                'listTitle' => sprintf('Wpisy autora: "%s"', $author),
            )
        );
    }

    /**
     * @Route("/{slug}", name="blog_post")
     *
     */
    public function postAction(Request $request, $slug)
    {

        // Wyświetlanie wszystkich postów
//        $PostRepo =$this->getDoctrine()->getRepository(Post::class);
//        $Post = $PostRepo->findOneBySlug($slug);
//
//        if(null === $Post){
//            throw $this->createNotFoundException('Post nie został znaleziony');
//        }

        //Showing published Posts

        $PostRepo = $this->getDoctrine()->getRepository(Post::class);
        $Post = $PostRepo->getPublishedPost($slug);

        if (null === $Post) {
            throw $this->createNotFoundException('Post nie został znaleziony');
        }


        // Comment section
        if (null !== $this->getUser()) {

            $Comment = new Comment();
            $Comment->setAuthor($this->getUser())
                ->setPost($Post);

            $commentForm = $this->createForm(CommentType::class, $Comment);

            if ($request->isMethod('POST')) {
                $commentForm->handleRequest($request);

                if ($commentForm->isValid()) {
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($Comment);
                    $em->flush();

                    $this->get('session')->getFlashBag()->add('success', ' Komentarz dodany');
                    $redirectUrl = $this->generateUrl(
                        'blog_post',
                        array(
                            'slug' => $Post->getSlug(),
                        )
                    );

                    return $this->redirect($redirectUrl);
                }
            }
        }

        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            $csrfProvider = $this->get('security.csrf.token_manager');
        }


        return $this->render(
            'WynajemBlogBundle:Posts:post.html.twig',
            array(
                'post' => $Post,
                'commentForm' => isset($commentForm) ? $commentForm->createView() : null,
                'csrfProvider' => isset($csrfProvider) ? $csrfProvider : null,
                'tokenName' => 'delCom%d',
            )
        );
    }



    /**
     * @Route(
     *     "/post/comment/delete/{commentId}/{token}",
     *     name="blog_deleteComment"
     * )
     * @return JsonResponse
     */
    public function deleteCommentAction($commentId, $token)
    {
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Nie masz uprawnień do tego zadania');
        }

        $validToken = sprintf('delCom%d', $commentId);

        if (!$this->isCsrfTokenValid($validToken, $token)) {
            throw $this->createAccessDeniedException('Błędny token akcji');
        }

        $Comment = $this->getDoctrine()->getRepository(Comment::class)->find($commentId);

        if (null == $Comment) {
            throw $this->createNotFoundException('Nie znaleziono takiego komentarza');
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($Comment);
        $em->flush();

        return new JsonResponse(
            array(
                'status' => 'ok',
                'message' => 'Wiadomość została usunięta',
            )
        );
    }

    public function getPaginationPosts(array $params = array(), $page)
    {
        $PostRepo = $this->getDoctrine()->getRepository('WynajemBlogBundle:Post');
        $qb = $PostRepo->getQueryBuilder($params);

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate($qb, $page, $this->itemsLimit);

        return $pagination;
    }

}
