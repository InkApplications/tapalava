<?php

namespace Tapalava\ScheduleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use JMS\SecurityExtraBundle\Annotation\Secure;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="home")
     * @Secure(roles="ROLE_USER")
     */
    public function defaultAction()
    {
        return $this->render('ScheduleBundle:Default:default.html.twig');
    }
}
