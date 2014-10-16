<?php

namespace LasVenturas\BlackjackBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class HomeController extends Controller
{
    public function indexAction()
    {
    	$name = 'romain';
        return $this->render('LasVenturasBlackjackBundle:Default:index.html.twig', array('name' => $name));
    }
}
