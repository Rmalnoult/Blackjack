<?php

namespace LasVenturas\BlackjackBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use LasVenturas\BlackjackBundle\Entity\User;


class GameController extends Controller
{
    public function initGameAction()
    {
    	var_dump('game initialized');
    	// get user wallet content
        $repository = $this->getDoctrine()
            ->getRepository('LasVenturasBlackjackBundle:User');

        $user = $repository->findOneByName($cookie);
               
    	// offer to choose a bet (range)
    	// render view with button start the game
    }
    public function beginRoundAction()
    {
    	var_dump('Round begining');
    	// initialize deck of cards
    	// get a random card
    	// render the view + links : hit me / that's ok
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
