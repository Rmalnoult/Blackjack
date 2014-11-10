<?php

namespace LasVenturas\BlackjackBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use LasVenturas\BlackjackBundle\Entity\User;
use LasVenturas\BlackjackBundle\Entity\UserRepository;
use LasVenturas\BlackjackBundle\Entity\Round;
use LasVenturas\BlackjackBundle\Entity\Revealedcards;
use LasVenturas\BlackjackBundle\Entity\RevealedcardsRepository;

class GameController extends Controller
{
    public function indexAction(Request $request)
    {
        // play isLoggedIn function to see if a cookie is found
        // isLoggedIn is in a service called Login control
        $LoginControlService = $this->get('las_venturas_blackjack.loginControl');

        if ($LoginControlService->isLoggedIn($request)) {
            // var_dump('loggedIn');
            $user = $LoginControlService->whoIsThisUser($request);
            return $this->initGame($user);
        } else {
            // var_dump('not loggedIn');
            return $LoginControlService->redirectToHome();
        }
    }
    public function initGame($userName) {
        // var_dump('game initializing');
        // initialize new round entity
        $round = new Round();

        // doctrine's syntax to load the User
        $userRepository = $this->getDoctrine()
            ->getRepository('LasVenturasBlackjackBundle:User');
        // get the user
        $user = $userRepository->findOneByName($userName);
        // get the user's credit
        $credit = $user->getWallet();

        // create a form to choose a bet    
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
            // get wallet
            $userWallet = $user->getWallet();

            // if bet is superior to credit available : render default view
            if ($userBet > $userWallet) {
		        return $this->render('LasVenturasBlackjackBundle:Game:pregame.html.twig', array(
		            'form' => $form->createView(),
		            'name' => $userName,
		            'credit' => $credit
		        ));             	
            }
            // take the bet out of his wallet
            $userWallet = $userWallet - $userBet;
            $user->setWallet($userWallet);
            // store the userid in the round db
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
            //pushing card2 in the playerCards array
	        array_push($playerCards, $card2);
            // calculate score of playercards
	        $playerScore = $this->getPlayerScore($playerCards);

            // store round repo in a variable to use it to call entity methods just below
            $roundRepository = $em->getRepository('LasVenturasBlackjackBundle:Round');

            $playerVariablesToRender = array(
                        'playerCards' =>  $playerCards,
                        'name' => $userName,
                        'playerScore' => $playerScore,
                        'roundId' => $roundId
                        );
	        if ($playerScore > 21){
                // make actions corresponding to a lost game 
                $roundRepository->playerLoses($roundId);
	        	return $this->render('LasVenturasBlackjackBundle:Game:lose.html.twig',$playerVariablesToRender);
	        } else {
	        	if($playerScore == 21) {
	        		$roundRepository->playerWins($roundId, $userName);
	        		return $this->render('LasVenturasBlackjackBundle:Game:win.html.twig',$playerVariablesToRender);
	        	} else {

			        return $this->render('LasVenturasBlackjackBundle:Game:game.html.twig', $playerVariablesToRender); 
	        	}
	        }
        }
        // render view with button start the game   
        return $this->render('LasVenturasBlackjackBundle:Game:pregame.html.twig', array(
            'form' => $form->createView(),
            'name' => $userName,
            'credit' => $credit
        )); 
    }

	public function getRandomCard($deck, $roundId) 
	{

		// get a random card id
		$cardId = rand(1, 52);

        $RevealedCardsRepository = $this->getDoctrine()->getRepository('LasVenturasBlackjackBundle:Revealedcards');

		// si la carte est déjà sortie, on relance la fonction getrandomcard()
		if ($RevealedCardsRepository->cardCameOutAlready($roundId, $cardId)){
			return $this->getRandomCard($deck, $roundId);
		}
		$card = $deck[$cardId];
		// store the card that was just revealed in database
		// query is in entity repository
        $RevealedCardsRepository->storeRevealedCard($roundId, $cardId, $deck);
		// renvoie la nouvelle carte
		return $card;

	}
	public function getPlayerScore($playerCards)
	{
        // initialize score
		$score = 0;
		// Add up all player cards value to get the score
		foreach ($playerCards as $playerCard) {
			$score = $score + $playerCard['value'];
            // var_dump($playerCard['value']);
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
        $roundRepository = $em->getRepository('LasVenturasBlackjackBundle:Round');
        $round = $roundRepository->find($roundId);
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
        // calculate playerScore with the new card 
        $playerScore = $this->getPlayerScore($playerCards);
        // save it in the Round object
       	$round->setPlayerScore($playerScore);
        // flush to update the changes permanently in the database
        $em->persist($round);
        $em->flush();

	    // foreach ($playerCards as $playerCard) {
	    // 	var_dump('player card'.$playerCard['card'].' of '.$playerCard['color']);
	    // }
	    $variablesToRender = array(
                'playerCards' =>  $playerCards,
                'name' => $userName,
                'playerScore' => $playerScore,
                'roundId' => $roundId
            );

		if ($playerScore == 21){
			$roundRepository->playerWins($roundId, $userName);
			return $this->render('LasVenturasBlackjackBundle:Game:win.html.twig', $variablesToRender);
		} else {  
			if ($playerScore > 21) {
				if ($this->hasAnAce($playerCards)){
					// si le joueur a plus de 21 et a un as, on change la valeur de la carte
					$playerCards = $this->changeAceValueTo1($playerCards);
					$playerScore = $this->getPlayerScore($playerCards);
                    // var_dump('playerscore '.$playerScore);
                    $variablesToRender = array_replace($variablesToRender, array('playerScore' =>$playerScore));
                    if ($playerScore < 21) {
                        return $this->render('LasVenturasBlackjackBundle:Game:game.html.twig', $variablesToRender); 
                    } else {
                        if ($playerScore == 21) {
                            $roundRepository->playerWins($roundId,$userName);
                            return $this->render('LasVenturasBlackjackBundle:Game:win.html.twig', $variablesToRender); 
                        } else {
                            if ($playerScore > 21) {
                                $roundRepository->playerLoses($roundId);
                                return $this->render('LasVenturasBlackjackBundle:Game:lose.html.twig', $variablesToRender);
                            }
                        }
                    } 	
				}
                // make actions corresponding to a lost game
                $roundRepository->playerLoses($roundId);
				return $this->render('LasVenturasBlackjackBundle:Game:lose.html.twig', $variablesToRender);
			}
		}
		// if none of the above condition is respected => render the game with the new parameters
		return $this->render('LasVenturasBlackjackBundle:Game:game.html.twig', $variablesToRender); 			
	}



	public function finishAction($userName, $roundId, $playerScore)
	{
        $em = $this->getDoctrine()->getManager();
        // get the revealcards repo 
        $reaveledCards = $em->getRepository('LasVenturasBlackjackBundle:Revealedcards');
        // find revealcards -> this will return an array of card ids
        $preExistingCards = $reaveledCards->findByRoundId($roundId);
        // get the round repo (for the deck and the score)
        $roundRepository = $em->getRepository('LasVenturasBlackjackBundle:Round');
        $round = $roundRepository->find($roundId);
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
	        // var_dump('new bank card: '.$newCard['card'].' of '.$newCard['color']);
	        // push it in the bankcards array
	        array_push($bankCards, $newCard);
	        // calculate score of the cards
	        $bankScore = $this->getPlayerScore($bankCards);       	
        }

	    // var_dump('Bankscore : '.$bankScore);
        $variablesToRender = array(
                'playerCards' =>  $playerCards,
                'bankScore' => $bankScore,
                'bankCards' => $bankCards,
                'name' => $userName,
                'playerScore' => $playerScore,
                'roundId' => $roundId
                );

		if ($bankScore > 21){
			$roundRepository->playerWins($roundId, $userName);
			return $this->render('LasVenturasBlackjackBundle:Game:win.html.twig', $variablesToRender);
		} else {
			if ($bankScore > $playerScore){
                // make actions corresponding to a lost game
                $roundRepository->playerLoses($roundId);
				return $this->render('LasVenturasBlackjackBundle:Game:lose.html.twig', $variablesToRender);
			} else {
				if ($bankScore == $playerScore) {
					$this->tie($roundId, $userName);
					return $this->render('LasVenturasBlackjackBundle:Game:tie.html.twig', $variablesToRender);				
				} else {
					if ($bankScore < $playerScore) {
						$roundRepository->playerWins($roundId, $userName);
						return $this->render('LasVenturasBlackjackBundle:Game:win.html.twig', $variablesToRender);				
					}
				}
			}
		}	
	}

	public function tie($roundId, $userName)
	{
		// get the ititial bet and give it back to the user
		$em = $this->getDoctrine()->getManager();
		$round = $em->getRepository('LasVenturasBlackjackBundle:Round')->find($roundId);
        // get this round's bet
		$roundBet = $round->getBet();
        $round->setWinner('tie');
		// get the user's wallet 
		$user = $em->getRepository('LasVenturasBlackjackBundle:User')->findOneByName($userName);
		$userWallet = $user->getWallet();
        // add round's bet back to the user's wallet
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
        // loop through the user's deck searching for an ACE
		foreach ($cards as $card){
			if ($card['card'] == 'A'){
                // found an Ace in the user's deck of cards
				return true;
			}
		}
		return false;
	}
	public function changeAceValueTo1($cards)
	{
        // i keeps count and will be used as the index of the Ace card in the user's deck
		$i = -1;
        foreach ($cards as $card){
            $i++;
            if ($card['card'] == 'A'){
                //save color of that ACE card (needed just after to replace the entry)
                $color = $card['color'];
                break;
            }
        }
        // replace the value of that Ace card => 1
        $cards[$i] = array_replace($cards[$i], array('card' => 'A', 'color'=> $color, 'value' => 1));
        return $cards;
	}
}
