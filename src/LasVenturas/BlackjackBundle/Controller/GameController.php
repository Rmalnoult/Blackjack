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
            $user = $LoginControlService->whoIsThisUser($request);
            return $this->initGame($user);
        } else {
            var_dump('not loggedIn');
            return $this->signup($request);
        }
        


    }
    public function initGame($userName) {
        
        // initialize new round entity
        $round = new Round();
        // doctrine's syntax to load the User
        $repository = $this->getDoctrine()
            ->getRepository('LasVenturasBlackjackBundle:User');
        // get the user
        $user = $repository->findOneByName($userName);
        // get the user's credit
        $credit = $user->getWallet();        

        // create a form to choose a bet (range)    
        $form = $this->createFormBuilder($round)
            ->add('bet', 'number', array('label' => ' Bet : '))
            ->add('save', 'submit', array('label' => ' BET ! '))
            ->getForm();
        // get the validated form and handle it
        $request = $this->getRequest();
        $form->handleRequest($request);
        // if form is valid
        if ($form->isValid()) {
            var_dump('form validated, user is : '.$userName);




            // store the bet that the user chose
            $userBet = $round->getbet();

            // get the current user
            $em = $this->getDoctrine()->getManager();
            $user = $em->getRepository('LasVenturasBlackjackBundle:User')->findOneByName($userName);
            // take the bet out his wallet
            $userWallet = $user->getWallet();
            $userWallet = $userWallet - $userBet;
            $user->setWallet($userWallet);
            // flush to update the changes permanently in the database
            $em = $this->getDoctrine()->getManager();
            $em->persist($round);
            $em->flush();

        }

        var_dump('Playa : '.$userName);   
        var_dump('Credit : '.$credit);   
        // render view with button start the game   
        return $this->render('LasVenturasBlackjackBundle:Game:pregame.html.twig', array(
            'form' => $form->createView(),
            'name' => $userName,
            'credit' => $credit
        )); 




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
