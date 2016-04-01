<?php

namespace Common\UserBundle\Controller;

use Common\UserBundle\Exception\UserException;
use Common\UserBundle\Form\RememberPasswordType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Common\UserBundle\Form\LoginType;
use Common\UserBundle\Form\RegisterType;
use Common\UserBundle\Entity\User;


class LoginController extends BaseController
{


    /**
     * @Route("/login", name="blog_login")
     */
    public function loginAction(Request $request)
    {
        //Login form
        $Session = $this->get('session');

        if ($request->attributes->has(Security::AUTHENTICATION_ERROR)) {
            $loginError = $request->get(Security::AUTHENTICATION_ERROR);
        } else {
            $loginError = $Session->remove(Security::AUTHENTICATION_ERROR);
        }

        if (isset($loginError)) {
            $this->getSessionFlashBag()->add('error', $loginError->getMessage());
        }

        $loginForm = $this->createForm(
            LoginType::class,
            array(
                'username' => $Session->get(Security::LAST_USERNAME),
            )
        );

        //Remember Password Form
        $rememberPasswdForm = $this->createForm(new RememberPasswordType());

        if ($request->isMethod('POST')) {
            $rememberPasswdForm->handleRequest($request);
            if ($rememberPasswdForm->isValid()) {
                try {
                    $userEmail = $rememberPasswdForm->get('email')->getData();
                    $userManager = $this->get('user_manager');
                    $userManager->sendResetPasswordLink($userEmail);

                    $this->getSessionFlashBag()->add(
                        'success',
                        'Instrukcje resetowania hasla zostały wyslane na email'
                    );
                    return $this->redirect($this->generateUrl('blog_login'));
                } catch (UserException $exc) {
                    $error = new FormError($exc->getMessage());
                    $rememberPasswdForm->get('email')->addError($error);
                }
            }
        }

        //Register User Form
        $User = new User();
        $registerUserForm = $this->createForm(RegisterType::class, $User);

        if ($request->isMethod('POST')) {
            $registerUserForm->handleRequest($request);
            if ($registerUserForm->isValid()) {
                try {
                    $userManager = $this->get('user_manager');
                    $userManager->registerUser($User);

                    $this->getSessionFlashBag()->add(
                        'success',
                        'Konto zostało utworzone. Na Twoją skrzynkę pocztową została wysłana wiadomość aktywacyjna.'
                    );
                    return $this->redirect($this->generateUrl('blog_login'));

                } catch (UserException $ex) {
                    $this->getSessionFlashBag()->add('error', $ex->getMessage());
                }

            }
        }
        return $this->render(
            'CommonUserBundle:Login:login.html.twig',
            array(
                'loginForm' => $loginForm->createView(),
                'rememberPasswdForm' => $rememberPasswdForm->createView(),
                'registerUserForm' => $registerUserForm->createView(),
            )
        );

    }


    /**
     * @Route("/reset-password/{actionToken}", name="user_resetPassword")
     */
    public function resetPasswordAction($actionToken)
    {
        $this->tryCatchReset($actionToken, 'Na Twój adres email zostało wysłane nowe hasło');

        return $this->redirect($this->generateUrl('blog_login'));
    }

    /**
     * @Route("/user-activation/{actionToken}", name="user_activateAccount")
     *
     */
    public function activateAccountAction($actionToken)
    {
        try {
            $userManager = $this->get('user_manager');
            $userManager->activateAccount($actionToken);
            $this->getSessionFlashBag()->add('success', 'Twoje konto zostało aktywowane');

        } catch (UserException $ex) {
            $this->getSessionFlashBag()->add('error', $ex->getMessage());
        }
        return $this->redirect($this->generateUrl('blog_login'));
    }

    /**
     * @param $actionToken
     */
    public function tryCatchReset($actionToken, $message)
    {
        try {
            $userManager = $this->get('user_manager');
            $userManager->resetPassword($actionToken);
            $this->getSessionFlashBag()->add('success', $message);
        } catch (UserException $ex) {
            $this->getSessionFlashBag()->add('error', $ex->getMessage());
        }
    }
}
