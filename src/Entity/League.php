<?php

namespace Pool\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="league")
 */
class League
{
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=80, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="picture", type="string", length=80, nullable=true)
     */
    private $picture;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="Pool\Entity\Tournament", mappedBy="league")
     */
    private $tournament;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Pool\Entity\Team", mappedBy="leagues")
     */
    private $teams;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->tournament = new \Doctrine\Common\Collections\ArrayCollection();
        $this->teams = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return League
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
     * Set picture.
     *
     * @param string $picture
     *
     * @return League
     */
    public function setPicture($picture)
    {
        $this->picture = $picture;

        return $this;
    }

    /**
     * Get picture.
     *
     * @return string
     */
    public function getPicture()
    {
        return $this->picture;
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

    public function __toString()
    {
        return $this->name;
    }

    /**
     * Add tournament.
     *
     * @param \Pool\Entity\Tournament $tournament
     *
     * @return League
     */
    public function addTournament(\Pool\Entity\Tournament $tournament)
    {
        $this->tournament[] = $tournament;
        $tournament->setLeague($this);

        return $this;
    }

    /**
     * Remove tournament.
     *
     * @param \Pool\Entity\Tournament $tournament
     */
    public function removeTournament(\Pool\Entity\Tournament $tournament)
    {
        $this->tournament->removeElement($tournament);
    }

    /**
     * Get tournament.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTournament()
    {
        return $this->tournament;
    }

    /**
     * Add team.
     *
     * @param \Pool\Entity\Team $team
     *
     * @return League
     */
    public function addTeam(\Pool\Entity\Team $team)
    {
        $this->teams[] = $team;
        $team->addLeague($this);

        return $this;
    }

    /**
     * Remove team.
     *
     * @param \Pool\Entity\Team $team
     */
    public function removeTeam(\Pool\Entity\Team $team)
    {
        $this->teams->removeElement($team);
        $team->removeLeague($this);
    }

    /**
     * Get teams.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTeams()
    {
        return $this->teams;
    }
}
