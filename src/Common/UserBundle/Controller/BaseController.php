<?php

namespace Common\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class BaseController extends Controller
{


    /**
     * @return \Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface|\Symfony\Component\HttpFoundation\Session\SessionBagInterface
     */
    public function getSessionFlashBag()
    {
        return $this->get('session')->getFlashBag();
    }
}
