<?php

namespace LasVenturas\BlackjackBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use LasVenturas\BlackjackBundle\Entity\User;
use LasVenturas\BlackjackBundle\Entity\Round;


class GameController extends Controller
{
    public function indexAction(Request $request)
    {
    	var_dump('game initialized');
        // play isLoggedIn function to see if a cookie is found
        // isLoggedIn is in a service called Login control
        $LoginControlService = $this->get('las_venturas_blackjack.loginControl');

        if ($LoginControlService->isLoggedIn($request)) {
            var_dump('loggedIn');
            $user = $LoginControlService->beginRound($request);
            return $this->initGame($user);
        } else {
            var_dump('not loggedIn');
            return $LoginControlService->redirectToHome();
        }
    }

    public function hitMeAction()
    {
    	var_dump('hit me');
    	// get another random card from the deck
    	// compare it if it has been released or not during the round
    	// render the view with the user's score and the 2 links
    }
    public function thatsOkAction()
    {
    	// the bank's turn
    	// loop : get cards until it reaches 17
    	// bank's score is over 21 ?bankwins() :playerwins()
    }

    public function bankWins()
    {
    	// update user stats
    	// defeat+1, game played+1
    }
    public function playerWins()
    {
     	// update user stats
    	// victory+1, game played+1, wallet = wallet + bet*2   	
    }	

}
