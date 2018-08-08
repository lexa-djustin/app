<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Token
 *
 * @ORM\Entity
 */
class Token
{
    /**
     * @var integer
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="token", type="string", length=32, unique=true, nullable=false)
     */
    private $token;

    /**
     * @var User
     *
     * @ORM\OneToOne(targetEntity="User", inversedBy="token")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @var
     *
     * @ORM\Column(name="date_added", type="date")
     */
    private $dateAdded;

    /**
     * @var
     *
     * @ORM\Column(name="last_request_date", type="date")
     */
    private $lastRequestDate;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * @param string $token
     */
    public function setToken(string $token)
    {
        $this->token = $token;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user)
    {
        $this->user = $user;
    }

    /**
     * @return mixed
     */
    public function getDateAdded()
    {
        return $this->dateAdded;
    }

    /**
     * @param mixed $dateAdded
     */
    public function setDateAdded($dateAdded)
    {
        $this->dateAdded = $dateAdded;
    }

    /**
     * @return mixed
     */
    public function getLastRequestDate()
    {
        return $this->lastRequestDate;
    }

    /**
     * @param mixed $lastRequestDate
     */
    public function setLastRequestDate($lastRequestDate)
    {
        $this->lastRequestDate = $lastRequestDate;
    }

    public function __construct()
    {
        $this->token = md5(uniqid(__CLASS__, true));
        $this->dateAdded = $this->lastRequestDate = new \DateTime();
    }
}
