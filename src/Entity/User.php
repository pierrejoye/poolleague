<?php

namespace PickleWeb\Entity;

use PickleWeb\Auth\ProviderMetadata;

/**
 * Class User.
 */
class User implements \Serializable
{
    /**
     * @var string
     */
    protected $email;

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
    protected $isAdmin;

    /**
     * @var string
     */
    protected $isCaptain;


    /**
     * @return string
     */
    public function getId()
    {
        return strtolower(trim($this->email));
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     *
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @param bool
     * 
     * @return null
     */
    public function setIsAdmin($isAdmin)
    {
        this->isAdmin = (bool)$isAdmin;
    }

    /**
     * @return bool
     */
    public function isAdmin()
    {
        return $this->isAdmin;
    }


    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return User
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
