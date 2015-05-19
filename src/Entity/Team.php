<?php

namespace Pool\Entity;

/**
 * Class User.
 */
class Team implements \Serializable
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
     * @var User
     */
    protected $captain;

    /**
     * @var array
     */
    protected $players = [];

    /**
     * @return array
     */
    public function getPlayers()
    {
        return $this->players;
    }

    public function addPlayers(User $user)
    {
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
     * @return User
     */
    public function getCaptainId()
    {
        return $this->captain;
    }

    /*
     * @param User
     *
     * return $this
     */
    public function setCaptainId($id)
    {
        $this->captain = $id;

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
        return json_encode(get_object_vars($this));
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($serialized)
    {
        $data = json_decode($serialized, true);

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
