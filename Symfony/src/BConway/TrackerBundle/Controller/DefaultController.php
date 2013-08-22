<?php

namespace BConway\TrackerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('BConwayTrackerBundle:Default:index.html.twig', array('name' => $name));
    }
}
