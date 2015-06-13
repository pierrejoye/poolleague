<?php

namespace Pool\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="game")
 */
class Game
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
     * @var \Pool\Entity\Round
     *
     * @ORM\ManyToOne(targetEntity="Pool\Entity\Round", inversedBy="games")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="round_id", referencedColumnName="id", nullable=false)
     * })
     */
    private $round;

    /**
     * @ORM\ManyToOne(targetEntity="Pool\Entity\Team")
     * @ORM\JoinColumn(name="home_id", referencedColumnName="id", nullable=true)
     **/
    private $home;

     /**
      * @ORM\ManyToOne(targetEntity="Pool\Entity\Team")
      * @ORM\JoinColumn(name="visitor_id", referencedColumnName="id", nullable=true)
      **/
     private $visitor;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="Pool\Entity\Score", mappedBy="game", cascade={"persist"})
     */
    private $scores;

    /**
     * @var int
     *
     * @ORM\Column(name="score_home", type="integer", nullable=true)
     */
    private $scoreHome;

    /**
     * @var int
     *
     * @ORM\Column(name="score_visitor", type="integer", nullable=true)
     */
    private $scoreVisitor;

    public function __construct()
    {
        $this->scores = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Add score.
     *
     * @param \Pool\Entity\Score $score
     *
     * @return Game
     */
    public function addScore(\Pool\Entity\Score $score)
    {
        $this->scores[] = $score;

        return $this;
    }

    /**
     * Remove Score.
     *
     * @param \Pool\Entity\Score $score
     */
    public function removeScore(\Pool\Entity\Score $score)
    {
        $this->scores->removeElement($score);
    }

    /**
     * Get scores.
     *
     * @return \Common\Collections\Collection
     */
    public function getScores()
    {
        return $this->scores;
    }

    /**
     * Set visitor score.
     *
     * @param int
     *
     * @return Game
     */
    public function setScoreVisitor($score)
    {
        $this->scoreVisitor = $score;

        return $this;
    }

    /**
     * Get visitor score.
     *
     * @return int
     */
    public function getScoreVisitor()
    {
        return $this->scoreVisitor;
    }

    /**
     * Set home score.
     *
     * @param int
     *
     * @return Game
     */
    public function setScoreHome($score)
    {
        $this->scoreHome = $score;

        return $this;
    }

    /**
     * Get home score.
     *
     * @return int
     */
    public function getScoreHome()
    {
        return $this->scoreHome;
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
     * Set round.
     *
     * @param \Pool\Entity\Round $round
     *
     * @return \Pool\Entity\Game
     */
    public function setRound(\Pool\Entity\Round $round)
    {
        $this->round = $round;
    }

    /**
     * Get round.
     *
     * @return \Pool\Entity\Round
     */
    public function getRound()
    {
        return $this->round;
    }

    /**
     * Get round.
     *
     * @param \Pool\Entity\Round $round
     *
     * @return Game
     */
    public function setTournament(\Pool\Entity\Round $round)
    {
        $this->round = $round;

        return $this;
    }
}
