<?php

namespace Pool\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Tournament.
 *
 * @ORM\Table(name="tournament")
 * @ORM\Entity
 */
class Tournament
{
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=45, nullable=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=45, nullable=true)
     */
    private $type;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="Pool\Entity\Round", mappedBy="tournament", cascade={"persist"})
     */
    private $rounds;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \Pool\Entity\League
     *
     * @ORM\ManyToOne(targetEntity="Pool\Entity\League", inversedBy="tournament")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="league_id", referencedColumnName="id")
     * })
     */
    private $league;

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return Tournament
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set type.
     *
     * @param string $type
     *
     * @return Tournament
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Get rounds.
     *
     * @return int
     */
    public function getRounds()
    {
        return $this->rounds;
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set league.
     *
     * @param \Pool\Entity\League $league
     *
     * @return Tournament
     */
    public function setLeague(\Pool\Entity\League $league = null)
    {
        $this->league = $league;

        return $this;
    }

    /**
     * Get league.
     *
     * @return \Pool\Entity\League
     */
    public function getLeague()
    {
        return $this->league;
    }

    /**
     * Add round.
     *
     * @param \Pool\Entity\Round $round
     *
     * @return \Pool\Entity\Tournament
     */
    public function addRound(\Pool\Entity\Round $round)
    {
        $this->rounds[] = $round;
        $round->setTournament($this);
    }

    public function __toString()
    {
        return $this->name;
    }
}
