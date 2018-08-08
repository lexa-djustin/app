<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * User
 *
 * @ORM\Entity(repositoryClass="App\Repository\User")
 */
class User implements \JsonSerializable
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
     * @ORM\Column(name="username", type="string", length=100, unique=true, nullable=false)
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", nullable=false)
     */
    private $password;

    /**
     * @var Token
     *
     * @ORM\OneToOne(targetEntity="Token", mappedBy="user", cascade={"persist"}, orphanRemoval=true)
     */
    protected $token;

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
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @param string $username
     */
    public function setUsername(string $username)
    {
        $this->username = $username;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password)
    {
        $this->password = $password;
    }


    /**
     * @return string|null
     */
    public function getToken()
    {
        if ($this->token) {
            return $this->token->getToken();
        }

        return null;
    }

    /**
     * @return bool
     */
    public function hasToken()
    {
        return $this->token !== null;
    }

    public function createToken()
    {
        $token = new Token();
        $token->setUser($this);

        $this->token = $token;
    }

    public function clearToken()
    {
        $this->token = null;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return ['username' => $this->getUsername()];
    }
}
