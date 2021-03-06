<?php

namespace LasVenturas\BlackjackBundle\Entity;

use Doctrine\ORM\EntityRepository;

use LasVenturas\BlackjackBundle\Entity\User;
use LasVenturas\BlackjackBundle\Entity\UserRepository;

/**
 * RoundRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class RoundRepository extends EntityRepository
{
	public function getRoundsLost($userId)
	{
	    // ORM request : find by user + find by winner => bank
	    $roundRepository = $this->getEntityManager()->getRepository('LasVenturasBlackjackBundle:Round');
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
	// returns a number of rounds ended in a tie for a given user
	public function getRoundsTied($userId)
	{
		$roundRepository = $this->getEntityManager()->getRepository('LasVenturasBlackjackBundle:Round');
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
    // gets the number of rounds played by the user
    public function getRoundsPlayed($userId)
    {
    	$roundRepository = $this->getEntityManager()->getRepository('LasVenturasBlackjackBundle:Round');
        $roundsPlayed = $roundRepository->findByUser($userId);
        $roundsPlayed = count($roundsPlayed);
        // var_dump('rounds played : '.$roundsPlayed);
        return $roundsPlayed;
    }
    // gets the number of rounds won by the user
    public function getRoundsWon($userName)
    {
    	$roundRepository = $this->getEntityManager()->getRepository('LasVenturasBlackjackBundle:Round');
        $roundsWon = $roundRepository->findByWinner($userName);
        $roundsWon = count($roundsWon);
        // var_dump('rounds won : '.$roundsWon);
        return $roundsWon;
    }
    public function playerLoses($roundId)
    {
    	$em = $this->getEntityManager();
        // double the initial bet and add it to its score
        $roundRepository = $em->getRepository('LasVenturasBlackjackBundle:Round');
        $round = $roundRepository->find($roundId);
        $round->setWinner('bank');
        //flush
        $em->persist($round);
        $em->flush();
        return true;
    }
    public function playerWins($roundId, $userName)
    {
        // double the initial bet and add it to its score
        $em = $this->getEntityManager();
        $round = $em->getRepository('LasVenturasBlackjackBundle:Round')->find($roundId);
        // get user's bet
        $roundBet = $round->getBet();
        // store winner's name
        $round->setWinner($userName);
        
        $user = $em->getRepository('LasVenturasBlackjackBundle:User')->findOneByName($userName);
        // get user's credit
        $userWallet = $user->getWallet();
        // calculate new wallet with the game earnings
        $userWallet = $userWallet + $roundBet + $roundBet;
        // store new wallet in user's wallet
        $user->setWallet($userWallet);

        //flush
        $em->persist($user);
        $em->persist($round);
        $em->flush();
        return true;
    }
}
