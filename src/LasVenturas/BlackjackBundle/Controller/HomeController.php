<?php

namespace LasVenturas\BlackjackBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class HomeController extends Controller
{

    public function indexAction()
    {
    	// if user is auth with a cookie, get info from cookie, say hello, offer link to play a game
    	// else if no cookie is available, welcome new player, present a form to auth (just a username)and let's play a game.
    }
    public function login()
    {

    }
    public function signup()
    {

    }
}
