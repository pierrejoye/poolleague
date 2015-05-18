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
     * @var string
     */
    protected $captainId;

    /**
     * @var string
     */
    protected $teamId;

    /**
     * @var array
     */
    protected $players = [];

    /**
     * @return User
     */
    public function getCaptain()
    {
        return $this->email;
    }

    /**
     * @return array
     */
    public function getPlayers()
    {
    }

    public function addPlayers(User $user)
    {
        return $this;
    }


    /**
     * @return int
     */
    public function getId()
    {
		if (!$this->id) {
			$this->id = $redis->incr("team_ids");
		}
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
     * @return bool
     */
    public function setName($nickname)
    {
        $this->nickname = $nickname;

        return $this;
    }

	public function setCaptain($id)
	{
		$this->captainId = $id;
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
     * @return string
     */
    public function getTeamId()
    {
        return $this->teamId;
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
}

/* vim: set tabstop=4 shiftwidth=4 expandtab: fdm=marker */
