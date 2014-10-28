<?php

namespace LasVenturas\BlackjackBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use LasVenturas\BlackjackBundle\Entity\User;
use LasVenturas\BlackjackBundle\Entity\Round;
use LasVenturas\BlackjackBundle\Entity\Revealedcards;

class GameController extends Controller
{
    public function indexAction(Request $request)
    {
        // play isLoggedIn function to see if a cookie is found
        // isLoggedIn is in a service called Login control
        $LoginControlService = $this->get('las_venturas_blackjack.loginControl');

        if ($LoginControlService->isLoggedIn($request)) {
            var_dump('loggedIn');
            $user = $LoginControlService->whoIsThisUser($request);
            return $this->initGame($user);
        } else {
            var_dump('not loggedIn');
            return $LoginControlService->redirectToHome();
        }
        


    }
    public function initGame($userName) {
        var_dump('game initializing');
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


            // store the bet that the user chose
            $userBet = $round->getbet();

            // get the current user
            $em = $this->getDoctrine()->getManager();
            $user = $em->getRepository('LasVenturasBlackjackBundle:User')->findOneByName($userName);
            // take the bet out of his wallet
            $userWallet = $user->getWallet();
            $userWallet = $userWallet - $userBet;
            $user->setWallet($userWallet);
            // store the userid in the round object
            $userId = $user->getId();
            $round->setUser($userId);

            
            // flush to update the changes permanently in the database
            $em->persist($round);
            $em->persist($user);
            $em->flush();

	        // initialise le deck, 
	        $deck = $round->getDeck();
	        $roundId = $round->getId();
	        // var_dump('roundId: '.$roundId);
	        $userName = $user->getName();
	        $score = 0;
	        $card1 = $this->getRandomCard($deck, $roundId);
	        $playerCards = array($card1);
	        $card2 = $this->getRandomCard($deck, $roundId);
	        array_push($playerCards, $card2);

	        var_dump('playerCards: '.$playerCards[0]['card'].' of '.$playerCards[0]['color']);
	        var_dump('playerCards: '.$playerCards[1]['card'].' of '.$playerCards[1]['color']);

	        return $this->render('LasVenturasBlackjackBundle:Game:game.html.twig', array(
	        	'playerCards' =>  $playerCards,
	        	'name' => $userName
	        )); 
        }

        // var_dump('Playa : '.$userName);   
        // var_dump('Credit : '.$credit);   
        // render view with button start the game   
        return $this->render('LasVenturasBlackjackBundle:Game:pregame.html.twig', array(
            'form' => $form->createView(),
            'name' => $userName,
            'credit' => $credit
        )); 
    }

	public function getRandomCard($deck, $roundId) 
	{

		// get a random card
		$cardId = rand(1, 52);
		// var_dump('random card id: '.$cardId);

		// si la carte est déjà sortie, on relance la fonction getrandomcard()
		if ($this->cardCameOutAlready($roundId, $cardId)){
			$this->getRandomCard($deck, $roundId);
		}
		$card = $deck[$cardId];
		$this->storeRevealedCard($roundId, $cardId);
		return $card;

	}
	public function cardCameOutAlready($roundId, $cardId)
	{
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('LasVenturasBlackjackBundle:Revealedcards');
        $preExistingCard = $repository->findOneBy(array('roundId' => $roundId, 'cardId' => $cardId));
        if ($preExistingCard) {
        	return true;
        	var_dump('pre existing card: '. 'true');
        }
    	var_dump('pre existing card: '. 'false');
    	return false;
	}
	public function storeRevealedCard($roundId, $cardId)
	{
		// new revealed card
		$revealedCard = new Revealedcards();
		$revealedCard->setCardId($cardId);
		$revealedCard->setRoundId($roundId);
		$em = $this->getDoctrine()->getManager();		
		$em->persist($revealedCard);
		$em->flush();

		// setRoundId($roundId)
		

		// setCardId
	}


}
