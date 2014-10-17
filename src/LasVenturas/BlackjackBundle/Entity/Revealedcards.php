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
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
}
