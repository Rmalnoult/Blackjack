<?php

namespace LasVenturas\BlackjackBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use LasVenturas\BlackjackBundle\Entity\Round;
use LasVenturas\BlackjackBundle\Entity\RoundRepository;
use LasVenturas\BlackjackBundle\Entity\User;


class StatsController extends Controller
{
    public function indexAction(Request $request)
    {
        // play isLoggedIn function to see if a cookie is found
        // isLoggedIn is in a service called Login control
        $LoginControlService = $this->get('las_venturas_blackjack.loginControl');

        if ($LoginControlService->isLoggedIn($request)) {
            // var_dump('loggedIn');
            $userName = $LoginControlService->whoIsThisUser($request);
            return $this->showUserStatsAction($userName);
        } else {
            // var_dump('not loggedIn');
            return $LoginControlService->redirectToHome();
        }
    }
    public function showUserStatsAction($userName)
    {

    	// var_dump('stats of '.$userName);
        $roundRepository = $this->getDoctrine()
            ->getRepository('LasVenturasBlackjackBundle:Round');

        $userRepository = $this->getDoctrine()
            ->getRepository('LasVenturasBlackjackBundle:User');
            
        $user = $userRepository->findOneByName($userName);
        $userId = $user->getId();

    	// get the number of round won
        $roundsWon = $roundRepository->getRoundsWon($userName);

        // get the number of rounds played
        $roundsPlayed = $roundRepository->getRoundsPlayed($userId);

    	// get the number of rounds lost
        $roundsLost = $roundRepository->getRoundsLost($userId);
        // echo '<pre>'; print_r($roundsLost); echo '</pre>';

        // get the number of tied games
        $roundsTied = $roundRepository->getRoundsTied($userId);

        // get top players (array ordered by credit)
        $topPlayers = $userRepository->getTopPlayers();

        // define an array with variables to render
        $variablesToRender = array(
                'roundsWon' =>  $roundsWon,
                'roundsTied' => $roundsTied,
                'roundsLost' => $roundsLost,
                'roundsPlayed' => $roundsPlayed,
                'userName' => $userName,
                'topPlayers' => $topPlayers,
                );
        // render stats view
        return $this->render('LasVenturasBlackjackBundle:Stats:stats.html.twig', $variablesToRender);
    	
    }
}
