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
     * @ORM\OneToMany(targetEntity="User", mappedBy="games_id")
     */
    protected $user;


  

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
     * Add products
     *
     * @param \LasVenturas\BlackjackBundle\Entity\User $products
     * @return Round
     */
    public function addProduct(\LasVenturas\BlackjackBundle\Entity\User $products)
    {
        $this->products[] = $products;

        return $this;
    }

    /**
     * Remove products
     *
     * @param \LasVenturas\BlackjackBundle\Entity\User $products
     */
    public function removeProduct(\LasVenturas\BlackjackBundle\Entity\User $products)
    {
        $this->products->removeElement($products);
    }

    /**
     * Get products
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getProducts()
    {
        return $this->products;
    }

    /**
     * Add user
     *
     * @param \LasVenturas\BlackjackBundle\Entity\User $user
     * @return Round
     */
    public function addUser(\LasVenturas\BlackjackBundle\Entity\User $user)
    {
        $this->user[] = $user;

        return $this;
    }

    /**
     * Remove user
     *
     * @param \LasVenturas\BlackjackBundle\Entity\User $user
     */
    public function removeUser(\LasVenturas\BlackjackBundle\Entity\User $user)
    {
        $this->user->removeElement($user);
    }

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
     * Constructor
     */
    public function __construct()
    {
        $this->user = new ArrayCollection();
    }

}
