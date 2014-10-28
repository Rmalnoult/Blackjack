<?php

namespace LasVenturas\BlackjackBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Revealedcards
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="LasVenturas\BlackjackBundle\Entity\RevealedcardsRepository")
 */
class Revealedcards
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
     * @ORM\Column(type="decimal", scale=0)
     */
    private $roundId;
    
    /**
     * @ORM\Column(type="decimal", scale=0)
     */
    private $cardId;


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
     * Set cardId
     *
     * @param string $cardId
     * @return Revealedcards
     */
    public function setCardId($cardId)
    {
        $this->cardId = $cardId;

        return $this;
    }

    /**
     * Get cardId
     *
     * @return string 
     */
    public function getCardId()
    {
        return $this->cardId;
    }



    /**
     * Get roundId
     *
     * @return \LasVenturas\BlackjackBundle\Entity\Round 
     */
    public function getRoundId()
    {
        return $this->roundId;
    }

    /**
     * Set roundId
     *
     * @param string $roundId
     * @return Revealedcards
     */
    public function setRoundId($roundId)
    {
        $this->roundId = $roundId;

        return $this;
    }
}
