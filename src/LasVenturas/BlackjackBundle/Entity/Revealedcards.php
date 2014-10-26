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
     * @ORM\ManyToOne(targetEntity="Round", inversedBy="user")
     * @ORM\JoinColumn(name="round_id", referencedColumnName="id")
     */
    private $roundId;
    
    /**
     * @ORM\Column(type="decimal", scale=2)
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
     * Set roundId
     *
     * @param \LasVenturas\BlackjackBundle\Entity\Round $roundId
     * @return Revealedcards
     */
    public function setRoundId(\LasVenturas\BlackjackBundle\Entity\Round $roundId = null)
    {
        $this->roundId = $roundId;

        return $this;
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
}
