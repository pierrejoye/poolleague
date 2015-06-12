<?php

namespace Pool\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="user")
 * @ORM\Entity
 */
class User
{
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=45, nullable=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="nickname", type="string", length=45, nullable=true)
     */
    private $nickname;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=45, nullable=true)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="cellphone", type="string", length=45, nullable=true)
     */
    private $cellphone;

    /**
     * @var string
     *
     * @ORM\Column(name="line", type="string", length=45, nullable=true)
     */
    private $line;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=80, nullable=true)
     */
    private $password;

    /**
     * @var int
     *
     * @ORM\Column(name="isadmin", type="integer", nullable=true)
     */
    private $isadmin;

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
     * @ORM\ManyToMany(targetEntity="Pool\Entity\Team", inversedBy="users")
     * @ORM\JoinTable(name="users_teams")
     */
    private $teams;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->teams = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set name.
     *
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
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set nickname.
     *
     * @param string $nickname
     *
     * @return User
     */
    public function setNickname($nickname)
    {
        $this->nickname = $nickname;

        return $this;
    }

    /**
     * Get nickname.
     *
     * @return string
     */
    public function getNickname()
    {
        return $this->nickname;
    }

    /**
     * Set email.
     *
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
     * Get email.
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set cellphone.
     *
     * @param string $cellphone
     *
     * @return User
     */
    public function setCellphone($cellphone)
    {
        $this->cellphone = $cellphone;

        return $this;
    }

    /**
     * Get cellphone.
     *
     * @return string
     */
    public function getCellphone()
    {
        return $this->cellphone;
    }

    /**
     * Set line.
     *
     * @param string $line
     *
     * @return User
     */
    public function setLine($line)
    {
        $this->line = $line;

        return $this;
    }

    /**
     * Get line.
     *
     * @return string
     */
    public function getLine()
    {
        return $this->line;
    }

    /**
     * Set password.
     *
     * @param string $password
     *
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = password_hash($password, PASSWORD_DEFAULT);

        return $this;
    }

    /**
     * Get password.
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Check password.
     *
     * @return string
     */
    public function checkPassword($password)
    {
        if (!password_verify($password, $this->password)) {
            return false;
        }

        return true;
    }

    /**
     * Set isadmin.
     *
     * @param int $isadmin
     *
     * @return User
     */
    public function setIsadmin($isadmin)
    {
        $this->isadmin = $isadmin;

        return $this;
    }

    /**
     * Get isadmin.
     *
     * @return int
     */
    public function isAdmin()
    {
        return $this->isadmin ? true : false;
    }

    /**
     * Get isadmin.
     *
     * @return int
     */
    public function getIsadmin()
    {
        return $this->isadmin;
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
     * Add team.
     *
     * @param \Pool\Entity\Team $team
     *
     * @return User
     */
    public function addTeam(\Pool\Entity\Team $team)
    {
        $this->teams[] = $team;

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

    public function __toString()
    {
        return $this->name;
    }
}
