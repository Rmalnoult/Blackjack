<?php

namespace LasVenturas\BlackjackBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use LasVenturas\BlackjackBundle\Entity\Round;
use LasVenturas\BlackjackBundle\Entity\User;


class StatsController extends Controller
{
    public function indexAction(Request $request)
    {
        // play isLoggedIn function to see if a cookie is found
        // isLoggedIn is in a service called Login control
        $LoginControlService = $this->get('las_venturas_blackjack.loginControl');

        if ($LoginControlService->isLoggedIn($request)) {
            var_dump('loggedIn');
            $userName = $LoginControlService->whoIsThisUser($request);
            return $this->showUserStats($userName);
        } else {
            var_dump('not loggedIn');
            return $LoginControlService->redirectToHome();
        }
    }
    public function showUserStats($userName)
    {

    	var_dump('stats of '.$userName);
        $roundRepository = $this->getDoctrine()
            ->getRepository('LasVenturasBlackjackBundle:Round');
        $userRepository = $this->getDoctrine()
            ->getRepository('LasVenturasBlackjackBundle:User');

    	// get the number of round won
        $roundsWon = $roundRepository->findByWinner($userName);
        $roundsWon = count($roundsWon);
        // get the number of rounds played

        $user = $userRepository->findOneByName($userName);
        $userId = $user->getId();
        $roundsPlayed = $roundRepository->findByUser($userId);
        $roundsPlayed = count($roundsPlayed);

    	// get the number of rounds lost




        // $roundsLost = $roundRepository->findByUser($userId

    	var_dump('rounds played : '.$roundsPlayed);
    	var_dump('rounds won : '.$roundsWon);
    	die;
    	
    }

}
