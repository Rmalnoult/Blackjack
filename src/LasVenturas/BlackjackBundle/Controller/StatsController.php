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
        $roundsWon = $this->getRoundsWon($roundRepository, $userName);

        // get the number of rounds played
        $roundsPlayed = $this->getRoundsPlayed($roundRepository, $userId);


    	// get the number of rounds lost
        $roundsLost = $this->getRoundsLost($roundRepository, $userId);
        // echo '<pre>'; print_r($roundsLost); echo '</pre>';

        // get the number of tied games
        $roundsTied = $this->getRoundsTied($roundRepository, $userId);

        // get top players (ordered by credit)
        $topPlayers = $this->getTopPlayers($userRepository);


        $variablesToRender = array(
                'roundsWon' =>  $roundsWon,
                'roundsTied' => $roundsTied,
                'roundsLost' => $roundsLost,
                'roundsPlayed' => $roundsPlayed,
                'userName' => $userName,
                'topPlayers' => $topPlayers,
                );

        return $this->render('LasVenturasBlackjackBundle:Stats:stats.html.twig', $variablesToRender);
    	
    }
    public function getRoundsWon($roundRepository, $userName)
    {
        $roundsWon = $roundRepository->findByWinner($userName);
        $roundsWon = count($roundsWon);
        // var_dump('rounds won : '.$roundsWon);
        return $roundsWon;
    }
    public function getRoundsPlayed($roundRepository, $userId)
    {
        $roundsPlayed = $roundRepository->findByUser($userId);
        $roundsPlayed = count($roundsPlayed);
        // var_dump('rounds played : '.$roundsPlayed);
        return $roundsPlayed;
    }
    public function getRoundsLost($roundRepository, $userId)
    {
        // ORM request : find by user + find by winner => bank
        $query = $roundRepository->createQueryBuilder('p')
            ->where('p.winner = :winner')
            ->andWhere('p.user = :user')
            ->setParameters(['winner' => 'bank', 'user' => $userId])
            ->getQuery();

        $roundsLost = $query->getResult();
        $roundsLost = count($roundsLost);
        // var_dump('rounds lost : '.$roundsLost);
        return $roundsLost;
    }
    public function getRoundsTied($roundRepository, $userId)
    {
        $query = $roundRepository->createQueryBuilder('p')
            ->where('p.winner = :winner')
            ->andWhere('p.user = :user')
            ->setParameters(['winner' => 'tie', 'user' => $userId])
            ->getQuery();

        $roundsTied = $query->getResult();
        
        $roundsTied = count($roundsTied);
        // var_dump('rounds tied : '.$roundsTied);
        return $roundsTied;
    }
    public function getTopPlayers($userRepository)
    {
        $topPlayers = $userRepository->findBy(array(), array('wallet' => 'DESC'),5);
        // echo '<pre>'; print_r($topPlayers); echo '</pre>';  
        return $topPlayers; 
    }

}
