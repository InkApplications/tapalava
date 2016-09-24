<?php

namespace Tapalava\ScheduleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Collection of informational/marketing pages.
 *
 * @author Maxwell Vandervelde <Max@MaxVandervelde.com>
 */
class DefaultController extends Controller
{
    /**
     * The Default page of the website.
     *
     * @Template
     * @Route("/", methods={"GET"}, name="default")
     */
    public function defaultAction()
    {
        return [];
    }
}
