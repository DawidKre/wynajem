<?php

namespace Wynajem\AdminBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends Controller
{
    /**
     * @Route(
     *      "/",
     *      name="admin_dashboard"
     * )
     *
     */
    public function indexAction()
    {
        return $this->render('WynajemAdminBundle:Dashboard:index.html.twig', array(
        ));
    }
}
