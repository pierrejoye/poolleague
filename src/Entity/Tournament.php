<?php

namespace Pool\Entity;

/**
 * Class Tournament.
 */
class Tournament
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
    protected $date;

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
     * @param string
     *
     * @return League
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * @return string
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * {@inheritdoc}
     */
    public function serialize2()
    {
        //        return json_encode(get_object_vars($this));
        return serialize(get_object_vars($this));
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize2($serialized)
    {
        //$data = json_decode($serialized, true);
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
