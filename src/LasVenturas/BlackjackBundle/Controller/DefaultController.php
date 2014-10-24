<?php

namespace LasVenturas\BlackjackBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    public function indexAction(Request $request)
    {
        
    	$session = $request->getSession();

		// store an attribute for reuse during a later user request
        // $session->set('blackJackPlayer', 'foo');

        var_dump('$session bj '.$session->get('blackJackPlayer'));
    	var_dump($session);
    	die;



    	$name = 'romain';

        return $this->render('LasVenturasBlackjackBundle:Default:index.html.twig', array('name' => $name));
    }
}
