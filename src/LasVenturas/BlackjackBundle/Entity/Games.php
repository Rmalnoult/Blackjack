<?php

namespace LasVenturas\BlackjackBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Games
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="LasVenturas\BlackjackBundle\Entity\GamesRepository")
 */
class Games
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
     * @ORM\OneToMany(targetEntity="User", mappedBy="games")
     */
    protected $score;

    public function __construct()
    {
        $this->score = new ArrayCollection();
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
}
