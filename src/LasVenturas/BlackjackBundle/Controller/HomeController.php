<?php

namespace LasVenturas\BlackjackBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ParameterBag;

class HomeController extends Controller
{

    public function indexAction(Request $request)
    {
    	// if user is auth with a cookie, get info from cookie, say hello, offer link to play a game
    	// else if no cookie is available, welcome new player, present a form to auth (just a username)and let's play a game.
        // var_dump($Response);
        var_dump($request->cookies);
        die;

        return $this->render('LasVenturasBlackjackBundle:Default:index.html.twig');

    }
    public function login()
    {

    }
    public function signup()
    {

    }
}
