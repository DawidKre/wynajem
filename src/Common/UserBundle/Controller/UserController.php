<?php

namespace Common\UserBundle\Controller;


use Common\UserBundle\Exception\UserException;
use Common\UserBundle\Form\AccountSettingType;
use Common\UserBundle\Form\ChangePasswordType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class UserController extends BaseController
{


    /**
     *
     * @Route(
     *     "/account-settings",
     *     name="user_accountSettings"
     * )
     *
     */
    public function accountSettingsAction(Request $request)
    {

        $User = $this->getUser();
        $accountSettingsForm = $this->createForm(new AccountSettingType(), $User);

        if ($request->isMethod('POST') && $request->request->has('accountSettings')) {
            $accountSettingsForm->handleRequest($request);

            if ($accountSettingsForm->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($User);

                $em->flush();

                $this->getSessionFlashBag()->add('success', 'Twoje dane zostały zmienione');

                return $this->redirect($this->generateUrl('user_accountSettings'));
            } else {
                $this->getSessionFlashBag()->add('error', 'Popraw błedy fomularza');
            }
        }

        //Change Password

        $changePasswordForm = $this->createForm(new ChangePasswordType(), $User);

        if ($request->isMethod('POST') && $request->request->has('changePassword')) {
            $changePasswordForm->handleRequest($request);

            if ($changePasswordForm->isValid()) {

                try {
                    $userManager = $this->get('user_manager');
                    $userManager->changePassword($User);

                    $this->getSessionFlashBag()->add('success', 'Twoje hasło zostało zmienione');

                    return $this->redirect($this->generateUrl('user_accountSettings'));

                } catch (UserException $ex) {
                    $this->getSessionFlashBag()->add('error', $ex->getMessage());
                }

            } else {
                $this->getSessionFlashBag()->add('error', 'Popraw błedy fomularza');
            }
        }

        return $this->render(
            'CommonUserBundle:User:accountSettings.html.twig',
            array(
                'user' => $User,
                'accountSettingsForm' => $accountSettingsForm->createView(),
                'changePasswordForm' => $changePasswordForm->createView(),


            )
        );
    }
}
