<?php

namespace Pool\Entity;

/**
 * Class League.
 */
class League implements \Serializable
{
    /**
     * @var string
     */
    protected $id = false;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $picture;

    /**
     * @var array
     */
    protected $teams;

    /**
     * @var array
     */
    protected $tournaments;

    /**
     * @return array
     */
    public function getTeams()
    {
        return $this->teams ? $this->teams : [];
    }

    public function setTeams($teams)
    {
        $this->teams = [];
        foreach ($teams as $team) {
            $this->teams[] = $team->getId();
        }

        return $this;
    }

    public function setTeamsFromId($teams)
    {
        $this->teams = [];
        foreach ($teams as $teamId) {
            $this->teams[] = $teamId;
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getTournaments()
    {
        return $this->tournaments;
    }

    public function addTournament(Tournament $tournament)
    {
        if (!is_array($this->tournaments)) {
            $this->tournaments = [];
        }
        $this->tournaments[$tournament->getDate()] = $tournament;

        return $this;
    }

    /**
     * @param int
     *
     * @return Team
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string
     *
     * @return Team
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getPicture()
    {
        return $this->picture;
    }

    /**
     * @param string $picture
     *
     * @return User
     */
    public function setPicture($picture)
    {
        $this->picture = $picture;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function serialize()
    {
        return serialize(get_object_vars($this));
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($serialized)
    {
        $data = unserialize($serialized);

        $fields = get_object_vars($this);
        foreach ($data as $key => $value) {
            if (array_key_exists($key, $fields)) {
                $this->$key = $value;
            }
        }
    }

    public function __toString()
    {
        return $this->getName();
    }
}

/* vim: set tabstop=4 shiftwidth=4 expandtab: fdm=marker */
