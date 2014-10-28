<?php

namespace LasVenturas\BlackjackBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;


/**
 * Round
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="LasVenturas\BlackjackBundle\Entity\RoundRepository")
 */
class Round
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="decimal", scale=2)
     */
    protected $bet;

    /**
     * @ORM\Column(type="decimal")
     */
    protected $user;


    protected $deck = array(
        0,
        array( 'card' => 'Ace', 'color' => 'spades', 'value' => 11),
        array( 'card' => '2', 'color' => 'spades', 'value' => 2),
        array( 'card' => '3', 'color' => 'spades', 'value' => 3),
        array( 'card' => '4', 'color' => 'spades', 'value' => 4),
        array( 'card' => '5', 'color' => 'spades', 'value' => 5),
        array( 'card' => '6', 'color' => 'spades', 'value' => 6),
        array( 'card' => '7', 'color' => 'spades', 'value' => 7),
        array( 'card' => '8', 'color' => 'spades', 'value' => 8),
        array( 'card' => '9', 'color' => 'spades', 'value' => 9),
        array( 'card' => '10', 'color' => 'spades', 'value' => 10),
        array( 'card' => 'Jack', 'color' => 'spades', 'value' => 10),
        array( 'card' => 'Queen', 'color' => 'spades', 'value' => 10),
        array( 'card' => 'King', 'color' => 'spades', 'value' => 10),

        array( 'card' => 'Ace', 'color' => 'clubs', 'value' => 11),
        array( 'card' => '2', 'color' => 'clubs', 'value' => 2),
        array( 'card' => '3', 'color' => 'clubs', 'value' => 3),
        array( 'card' => '4', 'color' => 'clubs', 'value' => 4),
        array( 'card' => '5', 'color' => 'clubs', 'value' => 5),
        array( 'card' => '6', 'color' => 'clubs', 'value' => 6),
        array( 'card' => '7', 'color' => 'clubs', 'value' => 7),
        array( 'card' => '8', 'color' => 'clubs', 'value' => 8),
        array( 'card' => '9', 'color' => 'clubs', 'value' => 9),
        array( 'card' => '10', 'color' => 'clubs', 'value' => 10),
        array( 'card' => 'Jack', 'color' => 'clubs', 'value' => 10),
        array( 'card' => 'Queen', 'color' => 'clubs', 'value' => 10),
        array( 'card' => 'King', 'color' => 'clubs', 'value' => 10),

        array( 'card' => 'Ace', 'color' => 'diamonds', 'value' => 11),
        array( 'card' => '2', 'color' => 'diamonds', 'value' => 2),
        array( 'card' => '3', 'color' => 'diamonds', 'value' => 3),
        array( 'card' => '4', 'color' => 'diamonds', 'value' => 4),
        array( 'card' => '5', 'color' => 'diamonds', 'value' => 5),
        array( 'card' => '6', 'color' => 'diamonds', 'value' => 6),
        array( 'card' => '7', 'color' => 'diamonds', 'value' => 7),
        array( 'card' => '8', 'color' => 'diamonds', 'value' => 8),
        array( 'card' => '9', 'color' => 'diamonds', 'value' => 9),
        array( 'card' => '10', 'color' => 'diamonds', 'value' => 10),
        array( 'card' => 'Jack', 'color' => 'diamonds', 'value' => 10),
        array( 'card' => 'Queen', 'color' => 'diamonds', 'value' => 10),
        array( 'card' => 'King', 'color' => 'diamonds', 'value' => 10),

        array( 'card' => 'Ace', 'color' => 'heart', 'value' => 11),
        array( 'card' => '2', 'color' => 'heart', 'value' => 2),
        array( 'card' => '3', 'color' => 'heart', 'value' => 3),
        array( 'card' => '4', 'color' => 'heart', 'value' => 4),
        array( 'card' => '5', 'color' => 'heart', 'value' => 5),
        array( 'card' => '6', 'color' => 'heart', 'value' => 6),
        array( 'card' => '7', 'color' => 'heart', 'value' => 7),
        array( 'card' => '8', 'color' => 'heart', 'value' => 8),
        array( 'card' => '9', 'color' => 'heart', 'value' => 9),
        array( 'card' => '10', 'color' => 'heart', 'value' => 10),
        array( 'card' => 'Jack', 'color' => 'heart', 'value' => 10),
        array( 'card' => 'Queen', 'color' => 'heart', 'value' => 10),
        array( 'card' => 'King', 'color' => 'heart', 'value' => 10),

        );

    public function getDeck()
    {
        return $this->deck;
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set bet
     *
     * @param string $bet
     * @return Round
     */
    public function setBet($bet)
    {
        $this->bet = $bet;

        return $this;
    }

    /**
     * Get bet
     *
     * @return string 
     */
    public function getBet()
    {
        return $this->bet;
    }

    /**
     * Add user
     *
     * @param \LasVenturas\BlackjackBundle\Entity\User $user
     * @return Round
     */

    /**
     * Get user
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUser()
    {
        return $this->user;
    }


    /**
     * Set user
     *
     * @param string $user
     * @return Round
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }
}
