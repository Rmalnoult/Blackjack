<?php

namespace LasVenturas\BlackjackBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use LasVenturas\BlackjackBundle\Entity\User;
use LasVenturas\BlackjackBundle\Entity\UserRepository;
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
        $userRepository = $this->getDoctrine()
            ->getRepository('LasVenturasBlackjackBundle:User');
        // get the user
        $user = $userRepository->findOneByName($userName);
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
            $round->setWinner('nobody');
            // initialise le score du round
            $round->setPlayerScore(0);
            $score = 0;

            // flush to update the changes permanently in the database
            $em->persist($round);
            $em->persist($user);
            $em->flush();

	        // initialise le deck, 
	        $deck = $round->getDeck();
	        $roundId = $round->getId();
	        // var_dump('roundId: '.$roundId);
	        $userName = $user->getName();
	        $card1 = $this->getRandomCard($deck, $roundId);
	        $playerCards = array($card1);
	        $card2 = $this->getRandomCard($deck, $roundId);
	        array_push($playerCards, $card2);
	        $playerScore = $this->getPlayerScore($playerCards);

            $playerVariablesToRender = array(
                        'playerCards' =>  $playerCards,
                        'name' => $userName,
                        'playerScore' => $playerScore,
                        'roundId' => $roundId
                        );

	        if ($playerScore > 21){
                $this->playerLoses($roundId, $userName);
	        	return $this->render('LasVenturasBlackjackBundle:Game:lose.html.twig',$playerVariablesToRender);
	        } else {
	        	if($playerScore == 21) {
	        		$this->playerWins($roundId, $userName);
	        		return $this->render('LasVenturasBlackjackBundle:Game:win.html.twig',$playerVariablesToRender);
	        	} else {

			        return $this->render('LasVenturasBlackjackBundle:Game:game.html.twig', $playerVariablesToRender); 
	        	}
	        }
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
            var_dump('card invalid : was already drawn');
        	return true;

        }
    	return false;
	}
	public function storeRevealedCard($roundId, $cardId)
	{
		// new revealed card
		$revealedCard = new Revealedcards();
		$revealedCard->setCardId($cardId);
		$revealedCard->setRoundId($roundId);
		// store revealed card in db
		$em = $this->getDoctrine()->getManager();		
		$em->persist($revealedCard);
		$em->flush();

	}
	public function getPlayerScore($playerCards)
	{
		$score = 0;
		// Add up all player cards value to get the score
		foreach ($playerCards as $playerCard) {
			$score = $score + $playerCard['value'];
		}
		return $score;
	}

	public function hitMeAction($userName, $roundId, $playerScore)
	{

        $em = $this->getDoctrine()->getManager();
        // get the revealcards repo 
        $reaveledCards = $em->getRepository('LasVenturasBlackjackBundle:Revealedcards');
        // find revealcards -> this will return an array of card ids
        $preExistingCards = $reaveledCards->findByRoundId($roundId);
        // get the round repo (for the deck and the score)
        $round = $em->getRepository('LasVenturasBlackjackBundle:Round')->find($roundId);
        // get the deck of cards
        $deck = $round->getDeck();
        // initialize playercards array
        $playerCards = array();
        // foreach card id found in the already revealed cards 
        // => push the corresponding card from the deck in the playercards arrayt
        foreach ($preExistingCards as $preExistingCard) {
        	$preExistingCardId = $preExistingCard->getCardId();
        	array_push($playerCards, $deck[$preExistingCardId]);
        }
        // generate a new random card
        $newCard = $this->getRandomCard($deck, $roundId);
        // push it in the playercards array
        array_push($playerCards, $newCard);
        // calculate playerScore based with the new card 
        $playerScore = $this->getPlayerScore($playerCards);
        // save it in the Round object
       	$round->setPlayerScore($playerScore);
        // flush to update the changes permanently in the database
        $em->persist($round);
        $em->flush();

	    foreach ($playerCards as $playerCard) {
	    	var_dump('player card'.$playerCard['card'].' of '.$playerCard['color']);
	    }
	    $variablesToRender = array(
                'playerCards' =>  $playerCards,
                'name' => $userName,
                'playerScore' => $playerScore,
                'roundId' => $roundId
            );

		if ($playerScore == 21){
			$this->playerWins();
			return $this->render('LasVenturasBlackjackBundle:Game:win.html.twig', $variablesToRender);
		} 
		else {  
			if ($playerScore > 21) {
				if ($this->hasAnAce($playerCards)){
					$this->changeAceValueTo1($playerCards);
					$playerScore = $this->getPlayerScore($playerCards);
					return $this->render('LasVenturasBlackjackBundle:Game:lose.html.twig', $variablesToRender); 	
				}
                $this->playerLoses($roundId, $userName);
				return $this->render('LasVenturasBlackjackBundle:Game:lose.html.twig', $variablesToRender);
			}
		}
		// if none of the above condition is respected => render the game with the new parameters
		return $this->render('LasVenturasBlackjackBundle:Game:game.html.twig', $variablesToRender); 			
	}



	public function finishAction($userName, $roundId, $playerScore)
	{
		// var_dump('player card1'.$playerCards[0]['card'].' of '.$playerCards[0]['color']);
		var_dump('playerscore : '.$playerScore);
        $em = $this->getDoctrine()->getManager();
        // get the revealcards repo 
        $reaveledCards = $em->getRepository('LasVenturasBlackjackBundle:Revealedcards');
        // find revealcards -> this will return an array of card ids
        $preExistingCards = $reaveledCards->findByRoundId($roundId);
        // get the round repo (for the deck and the score)
        $round = $em->getRepository('LasVenturasBlackjackBundle:Round')->find($roundId);
        // get the deck of cards
        $deck = $round->getDeck();
        // initialize playercards array
        $playerCards = array();
        // foreach card id found in the database 
        // => push the corresponding card from the deck to the playercards array
        foreach ($preExistingCards as $preExistingCard) {
        	$preExistingCardId = $preExistingCard->getCardId();
        	array_push($playerCards, $deck[$preExistingCardId]);
        }
        // initialize bankscore
        $bankScore = 0;
        $bankCards = array();
        while ($bankScore <= 17) {
	         // generate a new random card
	        $newCard = $this->getRandomCard($deck, $roundId);
	        var_dump('new bank card: '.$newCard['card'].' of '.$newCard['color']);
	        // push it in the bankcards array
	        array_push($bankCards, $newCard);
	        // calculate score of the cards
	        $bankScore = $this->getPlayerScore($bankCards);       	
        }

	    var_dump('Bankscore : '.$bankScore);
        $variablesToRender = array(
                'playerCards' =>  $playerCards,
                'bankScore' => $bankScore,
                'bankCards' => $bankCards,
                'name' => $userName,
                'playerScore' => $playerScore,
                'roundId' => $roundId
                );

		if ($bankScore > 21){
			$this->playerWins($roundId, $userName);
			return $this->render('LasVenturasBlackjackBundle:Game:win.html.twig', $variablesToRender);
		} else {
			if ($bankScore > $playerScore){
                $this->playerLoses($roundId, $userName);
				var_dump('bank wins because fuck you');
				return $this->render('LasVenturasBlackjackBundle:Game:lose.html.twig', $variablesToRender);
			} else {
				if ($bankScore == $playerScore) {
					$this->tie($roundId, $userName);
					return $this->render('LasVenturasBlackjackBundle:Game:tie.html.twig', $variablesToRender);				
				} else {
					if ($bankScore < $playerScore) {
						$this->playerWins($roundId, $userName);
						return $this->render('LasVenturasBlackjackBundle:Game:win.html.twig', $variablesToRender);				
					}
				}
			}
		}	
	}
	public function playerWins($roundId, $userName)
	{
		// double the initial bet and add it to its score
		$em = $this->getDoctrine()->getManager();
		$round = $em->getRepository('LasVenturasBlackjackBundle:Round')->find($roundId);
		$roundBet = $round->getBet();
        $round->setWinner($userName);
		
		$user = $em->getRepository('LasVenturasBlackjackBundle:User')->findOneByName($userName);
		$userWallet = $user->getWallet();
        // calculate new wallet with the game earnings
		$userWallet = $userWallet + $roundBet + $roundBet;
		$user->setWallet($userWallet);

		//flush
        $em->persist($user);
		$em->persist($round);
		$em->flush();
		return false;
	}
    public function playerLoses($roundId, $userName)
    {
        // double the initial bet and add it to its score
        $em = $this->getDoctrine()->getManager();
        $round = $em->getRepository('LasVenturasBlackjackBundle:Round')->find($roundId);
        $round->setWinner('bank');
        //flush
        $em->persist($round);
        $em->flush();
        return false;
    }
	public function tie($roundId, $userName)
	{
		// get the ititial bet and give it back to the user
		$em = $this->getDoctrine()->getManager();
		$round = $em->getRepository('LasVenturasBlackjackBundle:Round')->find($roundId);
		$roundBet = $round->getBet();
        $round->setWinner('tie');
		
		$user = $em->getRepository('LasVenturasBlackjackBundle:User')->findOneByName($userName);
		$userWallet = $user->getWallet();

		$userWallet = $userWallet + $roundBet;
		$user->setWallet($userWallet);

		//flush
        $em->persist($user);
		$em->persist($round);
		$em->flush();
		return false;
	}
	public function hasAnAce($cards)
	{
		foreach ($cards as $card){
			if ($card['card'] == 'Ace'){
				var_dump('ace found');
				return true;
			}
		}
		return false;
	}
	public function changeAceValueTo1($cards)
	{
		
		// x = indexof($card['Ace']
		var_dump('changing ace value to 1');
	}



	}
