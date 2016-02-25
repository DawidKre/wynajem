<?php

namespace Wynajem\BlogBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Wynajem\BlogBundle\Entity\Messages;
use Wynajem\BlogBundle\Form\ContactType;

class PagesController extends Controller
{
    /**
     * @Route("/index", name="blog_index")
     *
     */
    public function indexAction()
    {
        return $this->render(
            'WynajemBlogBundle:Pages:index.html.twig',
            array()
        );
    }

    /**
     * @Route("/contact", name="blog_contact")
     *
     */
    public function contactAction(Request $request)
    {
        $form = $this->createForm(ContactType::class);
        $Contact = new Messages();

        if (null !== $this->getUser()) {
            $User = $this->getUser();
            $form->setData(
                array(
                    'name' => $User->getUsername(),
                    'email' => $User->getEmail(),
                )
            );
        }

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $sendToEmail = $this->container->getParameter('contact_send_to');
                $sendFromEmail = $this->container->getParameter('mailer_user');
                $emailBody = $this->renderView(
                    'WynajemBlogBundle:Email:contact.html.twig',
                    array(
                        'name' => $form->get('name')->getData(),
                        'email' => $form->get('email')->getData(),
                        'message' => $form->get('message')->getData(),
                    )
                );

                $message = \Swift_Message::newInstance()
                    ->setSubject('[Wynajem] Kontakt')
                    ->setTo($sendToEmail)
                    ->setFrom($sendFromEmail, 'WynajemBlog')
                    ->setBody($emailBody, 'text/html');

                $this->get('mailer')->send($message);
                $this->get('session')->getFlashBag()->add('success', 'Twoja wiadomość została wysłana!');


                $Contact->setName($form->get('name')->getData())
                    ->setEmail($form->get('email')->getData())
                    ->setMessage($form->get('message')->getData());

                $em = $this->getDoctrine()->getManager();
                $em->persist($Contact);
                $em->flush();

                return $this->redirect($this->generateUrl('blog_contact'));
            }

        }

        return $this->render(
            'WynajemBlogBundle:Pages:contact.html.twig',
            array(
                'form' => $form->createView(),
            )
        );

    }

    /**
     * @Route("/about", name="blog_about")
     *
     */
    public function aboutAction()
    {
        return $this->render('WynajemBlogBundle:Pages:about.html.twig');
    }

    /**
     * @Route("/rent", name="blog_rent")
     *
     */
    public function rentAction()
    {
        return $this->render('WynajemBlogBundle:Pages:about.html.twig');
    }
}
