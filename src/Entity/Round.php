<?php

namespace Pool\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="round")
 */
class Round
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var date
     *
     * @ORM\Column(name="date", type="date")
     */
    private $date;

    /**
     * @var \Pool\Entity\Tourmanent
     *
     * @ORM\ManyToOne(targetEntity="Pool\Entity\Tournament", inversedBy="rounds")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="tournament_id", referencedColumnName="id")
     * })
     */
    private $tournament;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="Pool\Entity\Game", mappedBy="round", cascade={"persist"})
     */
    private $games;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->games = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set home.
     *
     * @param \Pool\Entity\Team $home
     *
     * @return Round
     */
    public function setHome(\Pool\Entity\Team $home = null)
    {
        $this->home = $home;

        return $this;
    }

    /**
     * Get home.
     *
     * @return \Pool\Entity\Team
     */
    public function getHome()
    {
        return $this->home;
    }

    /**
     * Set visitor.
     *
     * @param \Pool\Entity\Team $visitor
     *
     * @return Round
     */
    public function setVisitor(\Pool\Entity\Team $visitor = null)
    {
        $this->visitor = $visitor;

        return $this;
    }

    /**
     * Get visitor.
     *
     * @return \Pool\Entity\Team
     */
    public function getVisitor()
    {
        return $this->visitor;
    }

    /**
     * Set date.
     *
     * @param \Datetime $visitor
     *
     * @return Round
     */
    public function setDate(\Datetime $date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date.
     *
     * @return \Datetime
     */
    public function getDate()
    {
        return $this->date;
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
     * Add game.
     *
     * @param \Pool\Entity\Game $game
     *
     * @return Round
     */
    public function addGame(\Pool\Entity\Game $game)
    {
        $this->games[] = $game;

        return $this;
    }

    /**
     * Remove Game.
     *
     * @param \Pool\Entity\Game $game
     */
    public function removeGame(\Pool\Entity\Game $game)
    {
        $this->games->removeElement($game);
    }

    /**
     * Get games.
     *
     * @return \Common\Collections\Collection
     */
    public function getGames()
    {
        return $this->games;
    }

    /**
     * Set tournament.
     *
     * @param \Pool\Entity\Tournament $tournament
     *
     * @return \Pool\Entity\Round
     */
    public function setTournament(\Pool\Entity\Tournament $tournament)
    {
        $this->tournament = $tournament;
    }

    /**
     * Set tournament.
     *
     * @return \Pool\Entity\Tournament
     */
    public function getTournament()
    {
        return $this->tournament;
    }
}
