<?php

namespace Pool\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Team.
 *
 * @ORM\Table(name="team")
 * @ORM\Entity
 */
class Team
{
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=80, nullable=true)
     */
    private $name;

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
     * @ORM\ManyToMany(targetEntity="Pool\Entity\League", inversedBy="teams")
     * @ORM\JoinTable(name="teams_leagues")
     */
    private $leagues;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Pool\Entity\User", mappedBy="teams")
     */
    private $players;

    /**
     * @var Pool\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="team")
     * @ORM\JoinColumn(name="captain_id", referencedColumnName="id")
     */
    private $captain;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->leagues = new \Doctrine\Common\Collections\ArrayCollection();
        $this->players = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return Team
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
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set captain.
     *
     * @param \Pool\Entity\User $user
     *
     * @return Team
     */
    public function setCaptain(\Pool\Entity\User $user)
    {
        $this->captain = $user;

        return $this;
    }

    /**
     * Get captain.
     *
     * @return \Pool\Entity\User
     */
    public function getCaptain()
    {
        return $this->captain;
    }

    /**
     * Add league.
     *
     * @param \Pool\Entity\League $league
     *
     * @return Team
     */
    public function addLeague(\Pool\Entity\League $league)
    {
        $this->leagues[] = $league;

        return $this;
    }

    /**
     * Remove league.
     *
     * @param \Pool\Entity\League $league
     */
    public function removeLeague(\Pool\Entity\League $league)
    {
        $this->leagues->removeElement($league);
    }

    /**
     * Get leagues.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getLeagues()
    {
        return $this->leagues;
    }

    /**
     * Add player.
     *
     * @param \Pool\Entity\User $player
     *
     * @return Team
     */
    public function addPlayer(\Pool\Entity\User $player)
    {
        $player->addTeam($this);
        $this->players[] = $player;

        return $this;
    }

    /**
     * Remove player.
     *
     * @param \Pool\Entity\User $player
     */
    public function removePlayer(\Pool\Entity\User $player)
    {
        $this->players->removeElement($player);
        $player->removeTeam($this);
    }

    /**
     * Get players.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPlayers()
    {
        return $this->players;
    }

    public function __toString()
    {
        return $this->name;
    }
}
