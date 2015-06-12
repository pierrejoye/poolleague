<?php

namespace Pool\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="score")
 */
class Score
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
     * @ORM\ManyToOne(targetEntity="\Pool\Entity\User")
     * @ORM\JoinColumn(name="home_player_id", referencedColumnName="id", nullable=true)
     **/
    private $visitor;

    /**
     * @ORM\ManyToOne(targetEntity="\Pool\Entity\User")
     * @ORM\JoinColumn(name="visitor_player_id", referencedColumnName="id", nullable=true)
     **/
    private $home;

    /**
     * @var int
     *
     * @ORM\Column(name="score_home", type="integer")
     */
    private $scoreHome;

    /**
     * @var int
     *
     * @ORM\Column(name="score_visitor", type="integer")
     */
    private $scoreVisitor;

    /**
     * @var \Pool\Entity\Game
     *
     * @ORM\ManyToOne(targetEntity="\Pool\Entity\Game", inversedBy="scores")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="game_id", referencedColumnName="id", nullable=false)
     * })
     */
    private $game;

    /**
     * Set Home Player.
     *
     * @param \Pool\Entity\User $home
     *
     * @return Score
     */
    public function setHome(\Pool\Entity\User $home)
    {
        $this->home = $home;

        return $this;
    }

    /**
     * Get Home Player.
     *
     * @return \Pool\Entity\User
     */
    public function getHome()
    {
        return $this->home;
    }

    /**
     * Set visitor player.
     *
     * @param \Pool\Entity\User $visitor
     *
     * @return Score
     */
    public function setVisitor(\Pool\Entity\User $visitor)
    {
        $this->visitor = $visitor;

        return $this;
    }

    /**
     * Get Visitor.
     *
     * @return \Pool\Entity\User
     */
    public function getVisitor()
    {
        return $this->visitor;
    }

    /**
     * Set score Home.
     *
     * @param int $score
     *
     * @return Score
     */
    public function setScoreHome($score)
    {
        $this->scoreHome = $score;

        return $this;
    }

    /**
     * Get scorePlayer1.
     *
     * @return int
     */
    public function getScoreHome()
    {
        return $this->scoreHome;
    }

    /**
     * Set scorePlayer2.
     *
     * @param int $scorePlayer2
     *
     * @return Score
     */
    public function setScoreVisitor($score)
    {
        $this->scoreVisitor = $score;

        return $this;
    }

    /**
     * Get scorePlayer2.
     *
     * @return int
     */
    public function getScoreVisitor()
    {
        return $this->scoreVisitor;
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
     * Set game.
     *
     * @param \Pool\Doctrine\Entity\Game $game
     *
     * @return Score
     */
    public function setGame(\Pool\Entity\Game $game = null)
    {
        $this->game = $game;

        return $this;
    }

    /**
     * Get round.
     *
     * @return \Pool\Doctrine\Entity\Round
     */
    public function getGame()
    {
        return $this->game;
    }
}
